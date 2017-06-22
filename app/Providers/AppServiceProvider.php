<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      //
      // \Blade::setEchoFormat('e(utf8_encode(%s))');
      // https://stackoverflow.com/questions/29440737/php-init-class-with-a-static-function
      // https://laravel.com/docs/5.4/validation#custom-validation-rules
      Validator::extend('issensor', function ($attribute, $value, $parameters, $validator) {
        // verify that $value exists in Controller::sensors
        // dd([$attribute, $value, $parameters, $validator, in_array($value, $parameters)]);
        return in_array($value, $parameters);
        // return Controller::issensor($value);
      });
      Validator::replacer('issensor', function($message, $attribute, $rule, $parameters) {
        // dd([$message, $attribute, $rule, $parameters, str_replace(':attr', $attribute, $message)]);
        // no access to the failing value here :(
        return str_replace(':attr', $attribute, $message);
      });

      Validator::extend('only_custom', function ($attribute, $value, $parameters, $validator) {
        $form_attributes = $validator->attributes();
        // dd([$form_attributes, array_key_exists( 'graph_interval', $form_attributes), $form_attributes['graph_interval'] === 'custom');
        // When graph_interval is 'custom', $value must be a (string containing
        // a) positive integer
        return (array_key_exists( 'graph_interval', $form_attributes) &&
            $form_attributes['graph_interval'] === 'custom') ?
            (filter_var($value, FILTER_VALIDATE_INT) + 0 > 0) :
              true;
      });

    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
