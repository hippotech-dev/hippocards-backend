<?php

namespace App\Providers;

use App\Http\Services\GoogleService;
use App\Models\Article\Article;
use App\Models\Package\Baseklass;
use App\Models\Package\Sort;
use App\Models\Package\Word\Word;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'App\\Models\\Package\\Baseklass' => Baseklass::class,
            'App\\Models\\Package\\Sort' => Sort::class,
            'App\\Models\\Baseklass' => Baseklass::class,
            'App\\Models\\Sort' => Sort::class,
            'App\\Models\\Article\\Article' => Article::class,
        ]);
    }
}
