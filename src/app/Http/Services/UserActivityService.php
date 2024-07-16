<?php

namespace App\Http\Services;

use App\Enums\EUserActivityAction;
use App\Enums\EUserActivityType;
use App\Enums\EUserLoginType;
use App\Enums\EUserRole;
use App\Exceptions\AppException;
use App\Models\Package\Baseklass;
use App\Models\Package\Sort;
use App\Models\User\User;
use App\Models\Utility\UserActivity;

class UserActivityService
{
    protected function getFilterModel($filters)
    {
        return [
            "object_id" => [ "where", "object_id" ],
            "object_type" => [ "where", "object_type" ],
            "type" => [ "where", "type" ],
            "user_id" => [ "where", "user_id" ],
            "action" => [ "where", "action" ],
        ];
    }

    public function getUserActivities(User $user, array $filters, array $with = [], array $orderBy = [ "field" => "updated_at", "value" => "desc" ])
    {
        return filter_query_with_model($user->activities()->with($with), $this->getFilterModel($filters), $filters)->orderBy($orderBy["field"], $orderBy["value"])->get();
    }

    public function getUserActivitiesWithPage(User $user, array $filters, array $with = [], array $orderBy = [ "field" => "updated_at", "value" => "desc" ])
    {
        return filter_query_with_model($user->activities()->with($with), $this->getFilterModel($filters), $filters)->orderBy($orderBy["field"], $orderBy["value"])->simplePaginate(page_size());
    }

    public function getUserActivitiesByTypeWithPage(User $user, EUserActivityType $type, array $with = [], array $orderBy = [ "field" => "updated_at", "value" => "desc" ])
    {
        return $user->activities()->with($with)->where("type", $type)->orderBy($orderBy["field"], $orderBy["value"])->simplePaginate(page_size());
    }

    public function getUserActivitiesByTypeWithCursor(User $user, EUserActivityType $type, array $with = [], array $orderBy = [ "field" => "updated_at", "value" => "desc" ])
    {
        $orderBy = get_sort_info($orderBy);
        return UserActivity::with($with)->where("user_id", $user->id)->where("type", $type)->orderBy($orderBy["field"], $orderBy["value"])->cursorPaginate(page_size());
    }

    public function getUserActivity(User $user, array $filters, array $with = [])
    {
        return filter_query_with_model($user->activities()->with($with), $this->getFilterModel($filters), $filters)->first();
    }

    public function getActivityObjectByType(EUserActivityType $type, int $objectId)
    {
        switch ($type) {
            case EUserActivityType::USER_WORD:
                return Sort::find($objectId);
            case EUserActivityType::USER_PACKAGE:
                return Baseklass::find($objectId);
            default:
                throw new AppException("Invalid activity type!");
        }
    }

    public function createUpdateUserActivity(User $user, EUserActivityType $type, int $objectId, EUserActivityAction $action)
    {
        $object = $this->getActivityObjectByType($type, $objectId);

        if (is_null($object)) {
            throw new AppException("Invalid object!");
        }

        return $user->activities()->updateOrCreate(
            [
                "object_id" => $object->id ?? 0,
                "object_type" => get_class($object),
                "action" => $action,
            ],
            [
                "type" => $type,
                "updated_at" => date("Y-m-d H:i:s")
            ]
        );
    }

    public function createObjectActivity(User $user, mixed $object, EUserActivityType $type, EUserActivityAction $action)
    {
        return $user->activities()->create([
            "object_id" => $object->id,
            "object_type" => get_class($object),
            "action" => $action,
            "type" => $type
        ]);
    }
}
