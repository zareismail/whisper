<?php

namespace Zareismail\Whisper;
 

class Channel
{
    /**
     * The user Instance.
     * 
     * @var string|integer
     */
    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Instanciate with the given user.
     * 
     * @param string|integer
     * @return $this
     */
    public static function forUser($userId)
    {
        return new static($userId);
    }

    /**
     * The receive channel name.
     * 
     * @return string
     */
    public function receive()
    {
        return $this->channelName('receive');
    } 

    /**
     * Create cahnne for the given event name.
     * 
     * @param  string $channel 
     * @return string          
     */
    protected function channelName(string $channel)
    { 
        return "whisper.{$this->userId}.{$channel}";
    }
}
