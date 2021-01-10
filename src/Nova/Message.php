<?php

namespace Zareismail\Whisper\Nova; 

use Illuminate\Http\Request; 
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\{ID, Text, Number, DateTime, BelongsTo, HasMany}; 
use Zareismail\NovaContracts\Nova\User;

class Message extends Resource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Whisper\Models\WhisperMessage::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
    	return [
    		ID::make()->sortable(),   

            BelongsTo::make(__('Recipient'), 'recipient', User::class)
                ->withoutTrashed()
                ->readonly($request->viaRelationship() && $request->isMethod('get'))
                ->default(optional($request->findParentModel())->auth_id),

            $this->when($request->messenger && $request->isMethod('get'), function() {
                return Number::make('auth_id');
            }),

            Text::make(__('Write message'), 'message')
                ->sortable()
                ->required()
                ->rules('required'), 

            DateTime::make(__('Created At'), 'created_at')
                ->exceptOnForms(),

            DateTime::make(__('Updated At'), 'updated_at')
                ->exceptOnForms(), 

            DateTime::make(__('Sent At'), 'read_at')
                ->exceptOnForms(),

            DateTime::make(__('Read At'), 'read_at')
                ->exceptOnForms(),

            HasMany::make(__('Replies'), 'replies', static::class),
    	];
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    { 
        return $query
                ->where(function($query) use ($request) {
                    $query
                        ->where('auth_id', $request->user()->id)
                        ->where('recipient_id', $request->viaResourceId);
                })->orWhere(function($query) use ($request) { 
                    $query
                        ->where('auth_id', $request->viaResourceId)
                        ->where('recipient_id', $request->user()->id);
                }); 
    }  
}