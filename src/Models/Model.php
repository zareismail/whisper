<?php

namespace Zareismail\Whisper\Models;
   
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Zareismail\NovaContracts\Models\AuthorizableModel;
use Zareismail\NovaPolicy\Contracts\Ownable;

class Model extends AuthorizableModel implements HasMedia, Ownable
{ 
    use HasMediaTrait;  

	public function registerMediaCollections(): void
	{ 
	    $this->addMediaCollection('attachments');
	}
}
