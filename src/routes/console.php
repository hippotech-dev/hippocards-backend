<?php

use App\Http\Services\PackageService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('custom:add-sort-to-baseklass', function () {
    DB::transaction(function () {
        $oldPackages = DB::table("v0_baseklass")->where("sort", ">", 0)->get();
        foreach ($oldPackages as $package) {
            DB::table("baseklass")->where("id", $package->id)->update([
                "sort" => $package->sort
            ]);
        }
    });


})->purpose('Display an inspiring quote');
