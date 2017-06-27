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
        // dd($attribute, $value, $parameters, count($parameters), $validator, $form_attributes);

        // Coding error check: must have a parameter, and it must match the
        // name of one of the form attributes
        if (count($parameters) < 1 || !array_key_exists( $parameters[0], $form_attributes)) {
          dd('Invalid validation call arguments', $parameters, $form_attributes);
        }
        // When the parameter attribute is 'custom', $value must be a (string
        // containing a) positive integer
        return ($form_attributes[$parameters[0]] === 'custom') ?
            (filter_var($value, FILTER_VALIDATE_INT) + 0 > 0) :
            true;
      });

      Validator::extend('either', function ($attribute, $value, $parameters, $validator) {
        $form_attributes = $validator->attributes();
        // verify that at least one of the specified (in $parameters) form
        // attributes exists
        $found_attr = false;
        foreach($parameters as $attr_key) {
          if (array_key_exists( $attr_key, $form_attributes)) {
            $found_attr = true;
            break;
          }
        }
        // dd($attribute, $value, $parameters, $form_attributes, $found_attr);
        return $found_attr;
        // return Controller::issensor($value);
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
