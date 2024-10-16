<?php

namespace App\Http\Controllers;

use App\Http\Resources\LatestParkInfoResource;
use App\Http\Resources\ParkInfoResource;
use App\Models\LatestParkInformation;
use App\Models\ParkInformation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ParkController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'park_no' => 'nullable|string',
        ]);

        $model = ParkInformation::getModel();

        $model = $this->applyQuery($model, $data);

        $model = $this->applySorter($model, $data);

        return ParkInfoResource::collection($this->getPagination($model, $data));
    }

    public function indexLatest(Request $request)
    {
        $data = $request->validate([
            'park_no' => 'nullable|string',
        ]);

        $model = LatestParkInformation::with('park_information')->getModel();

        $model = $this->applyQuery($model, $data);

        $model = $this->applySorter($model, $data);

        return LatestParkInfoResource::collection($this->getPagination($model, $data));
    }

    protected function applyQuery(Model $model, array $data)
    {
        $qeury = $model->getQuery();
        if(isset($data['park_no'])) {
            $qeury->where('park_no', $data['park_no']);
            logger('park_no: ' . $data['park_no']);
        }

        return $model->setQuery($qeury);
    }

    protected function applySorter(Builder $model, array $data)
    {
        $sort = $data['sort'] ?? 'update_time';
        $order = $data['order'] ?? 'desc';

        return $model->orderBy($sort, $order);
    }

    protected function getPagination(Builder $model, array $data)
    {
        $page = $data['page'] ?? 1;
        $perPage = $data['per_page'] ?? 30;

        return $model->paginate($perPage, ['*'], 'page', $page);
    }
}
