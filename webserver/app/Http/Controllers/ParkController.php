<?php

namespace App\Http\Controllers;

use App\Http\Resources\LatestParkInfoResource;
use App\Http\Resources\ParkInfoResource;
use App\Http\Resources\PredictParkInfoResource;
use App\Models\LatestParkInformation;
use App\Models\ParkInformation;
use App\Models\PredictParkInformation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ParkController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'park_no' => 'nullable|string',
            'per_page' => 'nullable|integer|max:1440',
            'page' => 'nullable|integer',
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
            'per_page' => 'nullable|integer|max:1440',
            'page' => 'nullable|integer',
        ]);

        $model = LatestParkInformation::with('park_information')->getModel();

        $model = $this->applyQuery($model, $data);

        $model = $this->applySorter($model, $data);

        return LatestParkInfoResource::collection($this->getPagination($model, $data));
    }

    public function getPreds(Request $request)
    {
        $data = $request->validate([
            'park_no' => 'nullable|string',
            'per_page' => 'nullable|integer|max:1440',
            'page' => 'nullable|integer',
        ]);

        $data['future_time_gt'] = now();
        $data['sort'] = 'future_time';
        $data['order'] = 'asc';

        $query = PredictParkInformation::getModel();

        $query = $this->applyQuery($query, $data);

        $query = $this->applySorter($query, $data);

        return PredictParkInfoResource::collection($this->getPagination($query, $data));
    }

    public function storePred(Request $request)
    {
        $data = $request->validate([
            'park_no' => 'required|string',
            'future_time' => 'required|date',
            'free_quantity' => 'required|integer',
            'free_quantity_big' => 'required|integer',
            'free_quantity_mot' => 'required|integer',
            'free_quantity_dis' => 'required|integer',
            'free_quantity_cw' => 'required|integer',
            'free_quantity_ecar' => 'required|integer',
        ]);

        $predictPark = PredictParkInformation::where('park_no', $data['park_no'])
            ->where('future_time', $data['future_time'])
            ->first();

        if($predictPark) {
            $predictPark->update($data);
        }else{
            $predictPark = PredictParkInformation::create($data);
        }

        return PredictParkInfoResource::make($predictPark);
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

        if(isset($data['future_time_gt'])) {
            $qeury->where('future_time', '>', $data['future_time_gt']);
            logger('future_time_gt: ' . $data['future_time_gt']);
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
