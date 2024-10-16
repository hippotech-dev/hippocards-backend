<?php

use App\Models\Package\Word\Word;
use App\Util\Constant;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

if (!function_exists('cdn_path')) {
    function cdn_path(?string $path)
    {
        if (is_null($path)) {
            return null;
        }
        if ($path) {
            $path = '/' . trim($path, '/');
        }
        return urldecode(config('constants.CDN_URL') . $path);
    }
}

if (!function_exists('append_cdn_path')) {
    function append_cdn_path(?string $path)
    {
        if (is_null($path)) {
            return null;
        }
        if (str_starts_with($path, "https://")) {
            return $path;
        }
        if ($path) {
            $path = '/' . trim($path, '/');
        }
        return urldecode(Config::get("constants.CDN_URL") . $path);
    }
}

if (!function_exists('append_s3_path')) {
    function append_s3_path(?string $path)
    {
        if (is_null($path)) {
            return null;
        }
        if (str_starts_with($path, "https://")) {
            return $path;
        }
        if ($path) {
            $path = '/' . trim($path, '/');
        }
        return urldecode(Config::get("constants.CLOUDFRONT_URL") . $path);
    }
}

if (!function_exists("generate_phantom_model")) {
    function generate_phantom_model($class, $fieldsWithValues = null, $relations = null)
    {
        $object = new $class();
        if (!is_null($fieldsWithValues)) {
            foreach (array_keys($fieldsWithValues) as $field) {
                $object->{$field} = $fieldsWithValues[$field];
            }
        }

        if (!is_null($relations)) {
            foreach (array_keys($relations) as $relation) {
                $object->setRelation($relation, $relations[$relation]);
            }
        }
        return $object;
    }
}

if (!function_exists("error_handler")) {
    function error_handler(Exception $err)
    {
        Log::channel("custom")->error("CERROR: " . $err->getMessage() . " " . $err->getLine() . " " . $err->getFile());
        if ($err instanceof AuthorizationException) {
            return response()->unauthorized();
        }
        return response()->error();
    }
}

if (!function_exists("filter_query_with_model")) {
    function filter_query_with_model($query, array $model, array $params)
    {
        foreach (array_keys($params) as &$param) {
            $modelQuery = array_key_exists($param, $model) ? $model[$param] : null;
            if (is_null($modelQuery)) {
                continue;
            }
            switch (gettype($modelQuery)) {
                case "array":
                    if (count($modelQuery) < 2) {
                        continue 2;
                    }

                    $commands = $modelQuery[0];

                    if (count($modelQuery) === 3) {
                        $operator = $modelQuery[1];
                        $values = $modelQuery[2];
                    } else {
                        $values = $modelQuery[1];
                    }
                    // $configs = count($modelQuery) > 2 ? $modelQuery[2] : [];
                    switch (gettype($values)) {
                        case "array":
                            $queryNames = [];
                            $queryValues = [];
                            foreach ($values as $value) {
                                if (gettype($value) == "string") {
                                    array_push($queryNames, $value);
                                    array_push($queryValues, $params[$param]);
                                } elseif (gettype($value) == "array") {
                                    array_push($queryNames, $value["name"]);
                                    array_push($queryValues, $value["value"]);
                                }
                            }
                            break;
                        case "string":
                            $queryName = $values;
                            $queryValue = $params[$param];
                            break;
                        case "object":
                            if ($values instanceof Closure) {
                                $temp = $values($params[$param]);
                                $queryName = $temp[0];
                                $queryValue = $temp[1];
                            }
                            break;
                        default:
                            continue 3;
                    }
                    switch (gettype($commands)) {
                        case "array":
                            if (count($commands) != count($values)) {
                                continue 3;
                            }
                            for ($i = 0; $i < count($commands); $i++) {
                                $query = is_null($queryNames[$i])
                                    ? $query->{$commands[$i]}($queryValues[$i])
                                    : $query->{$commands[$i]}($queryNames[$i], $queryValues[$i]);
                            }
                            break;
                        case "string":
                            if (!isset($queryName) || !isset($queryValue)) {
                                continue 3;
                            }
                            if (isset($operator)) {
                                $query = $query->$commands($queryName, $operator, $queryValue);
                            } else {
                                $query = $query->$commands($queryName, $queryValue);
                            }
                            break;
                        default:
                            continue 3;
                    }
                    // no break
                default:
                    continue 2;
            }
        }
        return $query;
    }
}

if (!function_exists("convert_null_to_str")) {
    function convert_null_to_str(array $arr)
    {
        $keys = array_keys($arr);
        foreach ($keys as $key) {
            if (is_null($arr[$key])) {
                $arr[$key] = "";
            }
        }
        return $arr;
    }
}

if (!function_exists("date_diff_in_seconds")) {
    function date_diff_in_seconds(string $dateA, string $dateB)
    {
        $_dateA = new DateTime($dateA);
        $_dateB = new DateTime($dateB);

        return $_dateB->getTimestamp() - $_dateA->getTimestamp();
    }
}

if (!function_exists("date_diff_in_weeks")) {
    function date_diff_in_weeks(string $dateA, string $dateB)
    {
        return floor(date_diff_in_seconds($dateA, $dateB) / 60 / 60 / 24 / 7);
    }
}

if (!function_exists("date_diff_in_days")) {
    function date_diff_in_days(string $dateA, string $dateB)
    {
        $_dateA = new DateTime($dateA);
        $_dateB = new DateTime($dateB);

        return $_dateA->diff($_dateB)->days;
    }
}

if (!function_exists("date_diff_in_months")) {
    function date_diff_in_months(string $dateA, string $dateB)
    {
        $_dateA = new DateTime($dateA);
        $_dateB = new DateTime($dateB);

        return $_dateA->diff($_dateB)->m;
    }
}

if (!function_exists("fetch_url")) {
    function fetch_url(string $url, string $method, array $options)
    {
        $client = new Client();

        $response = $client->request(
            $method,
            $url,
            $options
        );

        return json_decode($response->getBody()->getContents(), true);
    }
}

if (!function_exists("get_array_value")) {
    function get_array_value($property, $array)
    {
        if (!array_key_exists($property, $array)) {
            return null;
        }
        return $array[$property];
    }
}

if (!function_exists("convert_array_to_object_with_key")) {
    function convert_array_to_object_with_key($array, $key)
    {
        $generatedObj = array();
        foreach ($array as $item) {
            if (!array_key_exists($key, $item)) {
                continue;
            }
            $generatedObj[$item[$key]] = $item;
            unset($generatedObj[$item[$key]][$key]);
        }
        return $generatedObj;
    }
}

if (!function_exists("week_day_index")) {
    function week_day_index(string $date)
    {
        $index = intval(date("w", strtotime($date))) - 1;
        return $index < 0 ? 6 : $index;
    }
}

if (!function_exists("generate_random_string_code")) {
    function generate_random_string_code(Closure $query)
    {
        $code = Str::random(6);

        // check if code exists
        do {
            $codeExists = $query($code);
            if ($codeExists) {
                $code = Str::random(6);
            }
        } while ($codeExists);
        return $code;
    }
}

if (!function_exists("check_email")) {
    function check_email(string $string)
    {
        return filter_var($string, FILTER_VALIDATE_EMAIL);
    }
}

if (!function_exists("get_class_map")) {
    function get_class_map(string $string)
    {
        $classMap = Config::get("constants.CLASS_MAP", []);

        if (!array_key_exists($string, $classMap)) {
            return null;
        }
        return $classMap[$string];
    }
}

if (!function_exists("get_object_by_id")) {
    function get_object_by_id(string $class, int $id)
    {
        return ($class)::find($id);
    }
}

if (!function_exists("cache_key")) {
    function cache_key(string $key, array $values = [])
    {
        return $key . "-" . implode("-", $values);
    }
}

if (!function_exists("gen_uuid")) {
    function gen_uuid()
    {
        return Str::uuid();
    }
}

if (!function_exists("generate_filter_schema")) {
    function generate_filter_schema($filters)
    {
        $keys = array_keys($filters);

        foreach ($keys as $key) {
            $operator = explode("_", $key);

            switch ($key) {

            }
        }
    }
}

if (!function_exists("page_size")) {
    function page_size()
    {
        return min($_GET["limit"] ?? Constant::DEFAULT_PAGINATION, 1000);
    }
}

if (!function_exists("get_sort_info")) {
    function get_sort_info($default  = [ "field" => "id", "value" => "desc" ])
    {
        return [ "field" => $_GET["_sort"] ?? $default["field"], "value" => $_GET["_order"] ?? $default["value"] ];
    }
}

if (!function_exists("get_role_middleware")) {
    function get_role_middleware(mixed $scopes)
    {
        $type = gettype($scopes);

        if ($type === "array") {
            return "role:" . implode(",", array_map(fn ($scope) => $scope->value, $scopes));
        } elseif ($type === "string") {
            return "role:" . $scopes;
        } elseif ($type === "object") {
            return "role:" . $scopes->value;
        }

        return "role";
    }
}

if (!function_exists("get_class_map_object")) {
    function get_class_map_object(int $objectId, string $type)
    {
        try {
            if (!array_key_exists($type, Constant::CLASS_MAP)) {
                return null;
            }
            $className = Constant::CLASS_MAP[$type];
            return $className::find($objectId);
        } catch (Exception $err) {
            return null;
        }
    }
}

if (!function_exists('check_const_level')) {
    function check_const_level($id)
    {
        switch ($id) {
            case "1":
                return [
                    "id" => 1,
                    "name" => "Анхан түвшин",
                    "color" => "#6172e2",
                ];
                break;
            case "2":
                return [
                    "id" => 2,
                    "name" => "Дунд түвшин",
                    "color" => "#fe6289",
                ];
                break;
            case "3":
                return [
                    "id" => 3,
                    "name" => "Ахисан түвшин",
                    "color" => "#afd325",
                ];
                break;
            case "4":
                return [
                    "id" => 4,
                    "name" => "Бүх түвшин",
                    "color" => "#afd325",
                ];
                break;
            default:
                return ["message" => "err"];
        }
    }
}

if (!function_exists('resource_append_additional')) {
    function resource_append_additional(mixed $resource, array $additionalValue, string $keyName = "additional")
    {
        return ($resource)->additional([ $keyName => $additionalValue ]);
    }
}

if (!function_exists('get_token_jti')) {
    function get_token_jti(mixed $resource, array $additionalValue, string $keyName = "additional")
    {
        return ($resource)->additional([ $keyName => $additionalValue ]);
    }
}
