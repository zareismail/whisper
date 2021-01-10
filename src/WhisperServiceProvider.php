<?php

namespace Zareismail\Whisper;
 
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova as LaravelNova;  
use Laravel\Nova\Events\ServingNova;  

class WhisperServiceProvider extends ServiceProvider  
{  
    /**
     * Register any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        LaravelNova::resources([
            Nova\Message::class,
        ]);
        LaravelNova::tools([
            Whisper::make(),
        ]);
        \Zareismail\NovaContracts\Models\User::resolveRelationUsing('messages', function($model) {
            return $model->hasMany(Models\WhisperMessage::class, 'auth_id');
        });
        $this->app->booted(function() {
            \Broadcast::channel('Whisper.Created', function () {
                return true;
            });  
        });
        Models\WhisperMessage::observe(Observers\WhisperMessage::class);
    } 
}
