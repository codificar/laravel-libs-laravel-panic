<?php

namespace Codificar\Panic\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PanicSettingSegupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($request == null) {
            $panicFailedMessage = trans('panic::panic.save_segup_setting_was_not_successful');
            return [
                $panicFailedMessage
            ];
        } else
            return [
                'success' => true,
                'segup_login' => $this->segup_login->value,
                'segup_password' => $this->segup_password->value,
                'segup_request_url' => $this->segup_request_url->value,
                'segup_verification_url' => $this->segup_verification_url->value,
            ];
    }
}
