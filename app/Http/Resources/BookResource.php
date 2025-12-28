<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'isbn' => $this->isbn,
            'description' => $this->description,
            'publication_date' => $this->publication_date,
            'genre' => $this->author->genre,
            'total_copies' => $this->author->total_copies,
            'available_copies' => $this->author->available_copies,
            'price' => $this->author->price,
            'cover_image' => $this->author->cover_image,
            'status' => $this->author->status,
            'is_available' => $this->isAvailable(),
            'author' => new AuthorResource($this->whenLoaded('author')),
        ];
    }
}
