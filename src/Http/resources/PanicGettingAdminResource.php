<?php

namespace Codificar\Panic\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PanicGettingAdminResource extends JsonResource
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
            'panic_admin_id' => $this->panic_admin_id,
            'panic_admin_phone_number' => $this->panic_admin_phone_number,
            'panic_admin_email' => $this->panic_admin_mail,
        ];
    }
}
