<?php

namespace App\Http\Controllers;

use App\Events\ParkInfoCreated;
use App\Http\Resources\LatestParkInfoResource;
use App\Http\Resources\ParkImageResource;
use App\Http\Resources\ParkInfoResource;
use App\Http\Resources\PredictParkInfoResource;
use App\Models\LatestParkInformation;
use App\Models\ParkImage;
use App\Models\ParkInformation;
use App\Models\PredictParkInformation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


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

    public function store(Request $request)
    {
        $data = $request->validate([
            'park_no' => 'required|string|max:255',
            'parking_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'business_hours' => 'required|string|max:255',
            'weekdays' => 'required|string|max:255',
            'holiday' => 'required|string|max:255',
            'free_quantity_big' => 'required|integer',
            'total_quantity_big' => 'required|integer',
            'free_quantity' => 'required|integer',
            'total_quantity' => 'required|integer',
            'free_quantity_mot' => 'required|integer',
            'total_quantity_mot' => 'required|integer',
            'free_quantity_dis' => 'required|integer',
            'total_quantity_dis' => 'required|integer',
            'free_quantity_cw' => 'required|integer',
            'total_quantity_cw' => 'required|integer',
            'free_quantity_ecar' => 'required|integer',
            'total_quantity_ecar' => 'required|integer',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'update_time' => 'required|date',
        ]);

        $parkInfo = ParkInformation::updateOrCreate([
            'park_no' => $data['park_no'],
            'update_time' => $data['update_time'],
        ], $data);

        ParkInfoCreated::dispatch($parkInfo);

        return ParkInfoResource::make($parkInfo);
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

        $model = PredictParkInformation::getModel();
        $model = $this->applyQuery($model, $data);
        $model = $this->applySorter($model, $data);

        return PredictParkInfoResource::collection($this->getPagination($model, $data));
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

    public function storeImage(Request $request)
    {
        $data = $request->validate([
            'park_no' => 'required|string',
            'image' => 'required|image',
            'captured_at' => 'required|date',
        ]);

        $path = $request->file('image')->store('park_images', 'public');

        $parkImage = ParkImage::create([
            'park_no' => $data['park_no'],
            'path' => $path,
            'url' => Storage::url($path),
            'captured_at' => $data['captured_at'],
        ]);

        return ParkImageResource::make($parkImage);
    }

    public function getImages(Request $request)
    {
        $data = $request->validate([
            'park_no' => 'nullable|string',
            'per_page' => 'nullable|integer|max:1440',
            'page' => 'nullable|integer',
            'recognized' => 'nullable|boolean',
        ]);
        $data['sort'] = 'captured_at';
        $data['recognized'] = $data['recognized'] ?? false;

        $model = ParkImage::getModel();
        $model = $this->applyQuery($model, $data);
        $model = $this->applySorter($model, $data);

        return ParkImageResource::collection($this->getPagination($model, $data));
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

        if(isset($data['recognized'])) {
            if($data['recognized'])
                $qeury->whereNotNull('recognition_result');
            else
                $qeury->whereNull('recognition_result');
            logger('recognized: ' . $data['recognized']);
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
