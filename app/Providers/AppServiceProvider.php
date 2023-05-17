<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        // Validator::extend('checExpensesBeforeEdit', function ($attribute, $value, $parameters, $validator) {
        //     $inputs = $validator->getData();

        //     // $query = User::where('phone', $concatenated_number);
        //     // if (!empty($except_id)) {
        //     //     $query->where('id', '<>', $except);
        //     // }

        //     // return $query->exists();
        //     return true;
        // });
    }
}
