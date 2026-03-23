<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateBookRequest;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::with('author');

        if ($request->filled('search')) {
            // $search = $request->input('search'); or
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('isbn', 'like', "%$search%")
                    ->orWhereHas('author', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });

            if ($request->filled('genre')) {
                $query->where('genre', $request->genre);
            }

            $books = $query->paginate(10);
            return BookResource::collection($books);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->validated());
        $book->load('author');
        return (new BookResource($book))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return new BookResource($book->load('author'));
    }

    // public function show(string $book) above is far better
    // {
    //     try {
    //         $book = Book::findOrFail($book);
    //         $book->load('author');
    //         return new BookResource($book);
    //     } catch (\Exception $th) {
    //         return response()->json(['status' => 'error', 'message' => 'Book not found'], 404);
    //     }
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->validated());
        $book->load('author');
        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(['status' => 'success', 'message' => 'Book deleted successfully'], 200);
    }

    public function listBooks()
    {
        $books = Book::latest()->take(10)->get();
        return BookResource::collection($books);
    }
}
