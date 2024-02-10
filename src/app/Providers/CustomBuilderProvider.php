<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class CustomBuilderProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('whereLike', function (string $attribute, string $searchTerm) {
            return $this->where($attribute, 'LIKE', "%{$searchTerm}%");
        });
        Builder::macro('moreThan', function (string $attribute, string $searchTerm) {
            return $this->where($attribute, '<=', $searchTerm);
        });
        Builder::macro('lessThan', function (string $attribute, string $searchTerm) {
            return $this->where($attribute, '>=', $searchTerm);
        });
        Builder::macro('orWhereLike', function (string $attribute, string $searchTerm) {
            return $this->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
        });
    }
}
