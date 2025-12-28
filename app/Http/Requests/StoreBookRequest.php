<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:13|unique:books,isbn',
            'description' => 'nullable|string',
            'publication_date' => 'nullable|date',
            'author_id' => 'required|exists:authors,id',
            'genre' => 'required|string|max:100',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cover_image' => 'nullable|string',
        ];
    }
}
