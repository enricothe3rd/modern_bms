<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'sector_name' => $this->sector_name,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
