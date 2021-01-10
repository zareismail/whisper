<?php

namespace Zareismail\Whisper;

use Laravel\Nova\Nova as LaravelNova;
use Laravel\Nova\Tool;

class Whisper extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        LaravelNova::script('zareismail-whisper', __DIR__.'/../dist/js/tool.js');
        LaravelNova::style('zareismail-whisper', __DIR__.'/../dist/css/tool.css');

        LaravelNova::provideToScript([
            'whisper' => [
                'users' => with(config('whisper.resource.users', \App\Nova\User::class), function($resource) {
                    return $resource::uriKey();
                }),
                'messages' => with(config('whisper.resource.messages', Nova\Message::class), function($resource) {
                    return $resource::uriKey();
                }),
            ],
        ]);
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return '<portal to="modals"><whisper /></portal>';
    }
}
