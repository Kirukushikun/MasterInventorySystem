<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        Validator::extend('greater_than_field', function ($attribute, $value, $parameters, $validator) {
            $otherFieldValue = $validator->getData()[$parameters[0]];
            return $value > $otherFieldValue;
        });

        Validator::replacer('greater_than_field', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':other_field', $parameters[0], $message);
        });

        Validator::extend('less_than_field', function ($attribute, $value, $parameters, $validator) {
            $otherFieldValue = $validator->getData()[$parameters[0]];
            return $value < $otherFieldValue;
        });

        Validator::replacer('less_than_field', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':other_field', $parameters[0], $message);
        });
        
        Validator::replacer('regex', function ($message, $attribute, $rule, $parameters) {
            return 'The ' . $attribute . ' field must contain only numbers';
        });
    }
}
