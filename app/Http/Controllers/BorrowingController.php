<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Http\Resources\BorrowingResource;
use App\Models\Book;
use App\Http\Requests\BorrowingRequest;

use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //filter by status
        $query = Borrowing::with(['member', 'book']);
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        //filter by member_id
        if ($request->has('member_id')) {
            $query->where('member_id', $request->member_id);
        }
        $borrowings = $query->latest()->paginate(15);

        return BorrowingResource::collection($borrowings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BorrowingRequest $request)
    {
        $book = Book::findOrFail($request->book_id);

        // Check if the book is available
        if (!$book->isAvailable()) {
            return response()->json(['message' => 'Book is not available for borrowing.'], 400);
        }
        // Create the borrowing record
        $borrowing = Borrowing::create($request->validated());

        //update book available copies
        $book->borrow();
        $borrowing->load(['member', 'book']);
        return new BorrowingResource($borrowing);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
