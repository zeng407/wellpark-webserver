<?php

namespace App\Http\Controllers;

use App\Events\ParkInfoCreated;
use App\Http\Resources\LatestParkInfoResource;
use App\Http\Resources\ParkImageResource;
use App\Http\Resources\ParkInfoResource;
use App\Http\Resources\PredictParkInfoResource;
use App\Models\ActivityInformation;
use App\Models\LatestParkInformation;
use App\Models\ParkImage;
use App\Models\ParkInformation;
use App\Models\PredictParkInformation;
use Carbon\Carbon;
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
            'update_time_gte' => 'nullable|date',
            'update_time_lte' => 'nullable|date',
        ]);

        $model = ParkInformation::getModel();
        $model = $this->applyQuery($model, $data);
        $model = $this->applySorter($model, $data);

        return ParkInfoResource::collection($this->getPagination($model, $data));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // required
            'park_no' => 'required|string|max:255',
            'free_quantity' => 'required|integer',
            'update_time' => 'required|date',

            // optional
            'parking_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'business_hours' => 'nullable|string|max:255',
            'weekdays' => 'nullable|string|max:255',
            'holiday' => 'nullable|string|max:255',
            'free_quantity_big' => 'nullable|integer',
            'total_quantity_big' => 'nullable|integer',
            'total_quantity' => 'nullable|integer',
            'free_quantity_mot' => 'nullable|integer',
            'total_quantity_mot' => 'nullable|integer',
            'free_quantity_dis' => 'nullable|integer',
            'total_quantity_dis' => 'nullable|integer',
            'free_quantity_cw' => 'nullable|integer',
            'total_quantity_cw' => 'nullable|integer',
            'free_quantity_ecar' => 'nullable|integer',
            'total_quantity_ecar' => 'nullable|integer',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'park_image_id' => 'nullable|integer',
        ]);

        $latestPark = LatestParkInformation::where('park_no', $data['park_no'])->first()?->park_information;


        $data['parking_name'] = $data['parking_name'] ?? $latestPark?->parking_name ?? '';
        $data['address'] = $data['address'] ?? $latestPark?->address ?? '';
        $data['business_hours'] = $data['business_hours'] ?? $latestPark?->business_hours ?? '';
        $data['weekdays'] = $data['weekdays'] ?? $latestPark?->weekdays ?? '';
        $data['holiday'] = $data['holiday'] ?? $latestPark?->holiday ?? '';
        $data['total_quantity_big'] = $data['total_quantity_big'] ?? $latestPark?->total_quantity_big ?? 0;
        $data['total_quantity'] = $data['total_quantity'] ?? $latestPark?->total_quantity ?? 0;
        $data['total_quantity_mot'] = $data['total_quantity_mot'] ?? $latestPark?->total_quantity_mot ?? 0;
        $data['total_quantity_dis'] = $data['total_quantity_dis'] ?? $latestPark?->total_quantity_dis ?? 0;
        $data['total_quantity_cw'] = $data['total_quantity_cw'] ?? $latestPark?->total_quantity_cw ?? 0;
        $data['total_quantity_ecar'] = $data['total_quantity_ecar'] ?? $latestPark?->total_quantity_ecar ?? 0;
        $data['free_quantity_cw'] = $data['free_quantity_cw'] ?? $latestPark?->free_quantity_cw ?? 0;
        $data['free_quantity_ecar'] = $data['free_quantity_ecar'] ?? $latestPark?->free_quantity_ecar ?? 0;
        $data['free_quantity_mot'] = $data['free_quantity_mot'] ?? $latestPark?->free_quantity_mot ?? 0;
        $data['free_quantity_dis'] = $data['free_quantity_dis'] ?? $latestPark?->free_quantity_dis ?? 0;
        $data['free_quantity_big'] = $data['free_quantity_big'] ?? $latestPark?->free_quantity_big ?? 0;
        $data['longitude'] = $data['longitude'] ?? $latestPark?->longitude ?? 0;
        $data['latitude'] = $data['latitude'] ?? $latestPark?->latitude ?? 0;

        $parkInfo = ParkInformation::updateOrCreate([
            'park_no' => $data['park_no'],
            'update_time' => $data['update_time'],
        ], $data);

        if(isset($data['park_image_id'])) {
            $parkImage = ParkImage::find($data['park_image_id']);
            if($parkImage) {
                $parkImage->update([
                    'park_information_id' => $parkInfo->id,
                    'recognition_result' => $parkInfo->free_quantity
                ]);
            }
        }

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
            'captured_at' => Carbon::parse($data['captured_at'])->format('Y-m-d H:i:s')
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

    public function getActivities(Request $request)
    {
        $data = $request->validate([
            'activitysdate' => 'nullable|string',
            'per_page' => 'nullable|integer|max:1440',
            'page' => 'nullable|integer',
        ]);
        $data['sort'] = 'activitysdate';
        $data['order'] = 'asc';

        $model = ActivityInformation::getModel();
        $model = $this->applyQuery($model, $data);
        $model = $this->applySorter($model, $data);

        return ParkInfoResource::collection($this->getPagination($model, $data));
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

        if(isset($data['update_time_gte'])) {
            $qeury->where('update_time', '>=', $data['update_time_gte']);
            logger('update_time_gte: ' . $data['update_time_gte']);
        }

        if(isset($data['update_time_lte'])) {
            $qeury->where('update_time', '<=', $data['update_time_lte']);
            logger('update_time_lte: ' . $data['update_time_lte']);
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
