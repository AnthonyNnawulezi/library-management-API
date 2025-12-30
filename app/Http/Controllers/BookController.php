<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with('author')->paginate(10);
        return BookResource::collection($books);;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->validated());
        $book->load('author');
        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $book)
    {
        try {
            $book = Book::findOrFail($book);
            $book->load('author');
            return new BookResource($book);
        } catch (\Exception $th) {
            return response()->json(['status' => 'error', 'message' => 'Book not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBookRequest $request, Book $book)
    {
        // $book = Book::findOrFail($book->id); why does it work withpout this line?
        $book->update($request->validated());
        $book->load('author');
        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        try {
            // $book = Book::findOrFail($book->id);
            $book->delete();
            return response()->json(['status' => 'success', 'message' => 'Book deleted successfully'], 200);
        } catch (\Exception $th) {
            return response()->json(['status' => 'error', 'message' => 'Book not found'], 404);
        }
    }
}
