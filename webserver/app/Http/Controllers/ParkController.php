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
            'long' => 'nullable|numeric',
            'lat' => 'nullable|numeric',
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

        if(isset($data['long']) && isset($data['lat'])) {
            $qeury->join('park_informations', 'latest_park_informations.park_information_id', '=', 'park_informations.id');
            $qeury->selectRaw('latest_park_informations.*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance', [$data['lat'], $data['long'], $data['lat']]);
            $qeury->orderBy('distance');
            logger('long: ' . $data['long'] . ', lat: ' . $data['lat']);
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
