<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->USER_ID,
            'email' => $this->EMAIL,
            'name' => $this->USERNAME,
            'icon_image' => $this->ICON_IMAGE,
        ];
    }
}
