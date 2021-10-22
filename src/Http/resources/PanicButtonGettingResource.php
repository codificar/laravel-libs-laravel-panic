<?php

namespace Codificar\Panic\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PanicButtonGettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'panic_button_enabled_user' => $this->panic_button_enabled_user->panic_button_enabled_user,
            'panic_button_enabled_provider' => $this->panic_button_enabled_provider->panic_button_enabled_provider,
        ];
    }
}
