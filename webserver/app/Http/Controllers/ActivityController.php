<?php

namespace App\Http\Controllers;


use App\Http\Resources\ActivityInformationResource;
use App\Models\ActivityInformation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'activitysdate_gte' => 'nullable|string',
            'per_page' => 'nullable|integer|max:1440',
            'page' => 'nullable|integer',
        ]);
        $data['sort'] = 'activitysdate';
        $data['order'] = 'asc';

        $model = ActivityInformation::getModel();
        $model = $this->applyQuery($model, $data);
        $model = $this->applySorter($model, $data);

        return ActivityInformationResource::collection($this->getPagination($model, $data));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'recognition_location' => 'required|string',
        ]);

        $activity = ActivityInformation::find($data['id']);
        if(!$activity) {
            return response()->json(['message' => 'Activity not found'], 404);
        }

        $activity->update([
            'recognition_location' => $data['recognition_location'],
        ]);

        return ActivityInformationResource::make($activity);
    }

    protected function applyQuery(Model $model, array $data)
    {
        $qeury = $model->getQuery();

        if(isset($data['activitysdate_gte'])) {
            $qeury->where('activitysdate', '>=', $data['activitysdate_gte']);
            logger('activitysdate_gte: ' . $data['activitysdate_gte']);
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
