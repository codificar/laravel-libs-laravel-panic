<?php

namespace Codificar\Panic\Models;

class Messages extends \Eloquent
{
    protected $guarded = ['id'];
	protected $table = 'messages';

    /**
     * will change message to viewed
     * @return void
     */
    public function setMessageAsSeen()
    {
        $this->is_seen = true;
        $this->save();
    }
	
}