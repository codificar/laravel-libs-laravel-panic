<?php

namespace Codificar\Panic\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PanicGettingSegupResource extends JsonResource
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
            'success' => true,
            'segup_login' => $this->segup_login,
            'segup_password' => $this->segup_password,
            'segup_request_url' => $this->segup_request_url,
            'segup_verification_url' => $this->segup_verification_url,
        ];
    }
}
