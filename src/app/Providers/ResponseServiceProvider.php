<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro("success", function ($data = null) {
            $response = ["success" => true];
            boolval($data) && $response["data"] = $data;
            return response()->json($response);
        });

        Response::macro("error", function ($error, $status_code = 500) {
            return response()->json(
                [
                    "success" => false,
                    "message" => $error,
                ],
                $status_code,
            );
        });

        Response::macro("fail", function ($data = null, $status_code = 400) {
            $response = ["success" => false];
            boolval($data) && $response["data"] = $data;

            return response()->json(
                $response,
                $status_code,
            );
        });

        Response::macro("notFound", function ($data = null, $status_code = 404) {
            return response()->json(
                [
                    "success" => false,
                    "message" => $data ?? "Not found",
                ],
                $status_code,
            );
        });

        Response::macro("successAppend", function ($data = []) {
            return response()->json(array_merge(["success" => true], $data));
        });

        Response::macro("failAppend", function ($data = [], $status_code = 400) {
            return response()->json(
                array_merge(["success" => false], $data),
                $status_code,
            );
        });

        Response::macro("error", function ($data = null, $code = 500) {
            return response()->json([
                "message" => "Алдаа гарлаа.",
                "success" => false
            ], $code);
        });

        Response::macro("unauthorized", function ($message = "Unauthorized to perform such action!", $status_code = 401) {
            return response()->json(
                array_merge([
                    "message" => $message,
                    "success" => false
                ]),
                $status_code,
            );
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
