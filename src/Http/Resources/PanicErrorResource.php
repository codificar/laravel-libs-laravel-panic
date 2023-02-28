<?php

namespace Codificar\Panic\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PanicErrorResource extends JsonResource
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
            'success' => false,
            'id' => null,
        ];
    }
}
