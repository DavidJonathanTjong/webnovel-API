<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NovelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request)
        return[
            'id' => $this->id,
            'nama_novel' => $this->nama_novel,
            'foto_sampul' => $this->foto_sampul,
            'deskripsi' => $this->deskripsi,
            'rating_novel' => $this->rating_novel,
            'created_at' => $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }
}