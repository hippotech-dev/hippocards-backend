<?php

namespace App\Http\Services;

use App\Enums\EUserActivityAction;
use App\Enums\EUserActivityType;
use App\Enums\EUserLoginType;
use App\Enums\EUserRole;
use App\Exceptions\AppException;
use App\Models\Package\Sort;
use App\Models\User\User;

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

    public function getUserActivities(User $user, array $filters, array $with = [], array $order = [ "column" => "updated_at", "value" => "desc" ])
    {
        return filter_query_with_model($user->activities()->with($with), $this->getFilterModel($filters), $filters)->orderBy($order["column"], $order["value"])->get();
    }

    public function getUserActivitiesWithPage(User $user, array $filters, array $with = [], array $order = [ "column" => "updated_at", "value" => "desc" ])
    {
        return filter_query_with_model($user->activities()->with($with), $this->getFilterModel($filters), $filters)->orderBy($order["column"], $order["value"])->simplePaginate(page_size());
    }

    public function getUserActivitiesByTypeWithPage(User $user, EUserActivityType $type, array $with = [], array $order = [ "column" => "updated_at", "value" => "desc" ])
    {
        return $user->activities()->with($with)->where("type", $type)->orderBy($order["column"], $order["value"])->simplePaginate(page_size());
    }

    public function getUserActivity(User $user, array $filters, array $with = [])
    {
        return filter_query_with_model($user->activities()->with($with), $this->getFilterModel($filters), $filters)->first();
    }

    public function getActivityObjectByType(EUserActivityType $type, int $objectId)
    {
        switch ($type) {
            case EUserActivityType::WORD:
                return Sort::find($objectId);
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
                "type" => 1,
                "updated_at" => date("Y-m-d H:i:s")
            ]
        );
    }
}
