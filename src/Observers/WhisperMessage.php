<?php

namespace Zareismail\Whisper\Observers;

use Zareismail\Whisper\Events\NewMessage;

class WhisperMessage
{
	public function created($model)
	{
		NewMessage::dispatch($model);
	}
}
