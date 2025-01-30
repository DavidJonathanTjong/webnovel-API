<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailNovelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return[
            'id' => $this->id,
            'id_novel' => $this->id_novel,
            'chapter_novel' => $this->chapter_novel,
            'text_novel' => $this->text_novel,
            'created_at' => $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }
}