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
      });
      Validator::replacer('issensor', function($message, $attribute, $rule, $parameters) {
        // dd([$message, $attribute, $rule, $parameters, str_replace(':attr', $attribute, $message)]);
        // no access to the failing value here :(
        return str_replace(':attr', $attribute, $message);
      });

      Validator::extend('only_custom', function ($attribute, $value, $parameters, $validator) {
        // Coding error check: must have a parameter
        if (count($parameters) < 1) {
          dd('Invalid validation call arguments: attribute name required', $parameters);
        }

        $found_attr = true;
        // dd($attribute, $value, $parameters, count($parameters), $validator);
        if ($value === 'custom') {
          // The source attribute value is 'custom', so (at least) one of the
          // passed parameters must be an attribute name that is (a string
          // containing) a postive integer.
          $form_attributes = $validator->attributes();
          $found_attr = false;
          foreach ($parameters as $attr_key) {
            if (array_key_exists( $attr_key, $form_attributes) &&
                filter_var($form_attributes[$attr_key], FILTER_VALIDATE_INT) + 0 > 0) {
              $found_attr = true;
              break;
            }
          }
        }
        // Either not 'custom', or found non-empty attribute
        return $found_attr;
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
