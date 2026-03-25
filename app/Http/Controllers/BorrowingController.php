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

        $borrowings = $query->latest()->paginate(15)->withQueryString();
        return BorrowingResource::collection($borrowings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BorrowingRequest $request)
    {
        $book = Book::findOrFail($request->book_id); //useless since it exists in the borrowing request

        // Check if the book is available
        if (!$book->isAvailable()) {
            return response()->json(['message' => 'Book is not available for borrowing.'], 409);
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
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['member', 'book']);
        return new BorrowingResource($borrowing);
    }


    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status === 'returned') {
            return response()->json(['message' => 'This book has already been returned.'], 400);
        }

        // Update the borrowing record
        $borrowing->status = 'returned';
        $borrowing->returned_date = now();
        $borrowing->save();
        //or
        // $borrowing->update([
        //     'status' => 'returned',
        //     'returned_date' => now(),
        // ]);

        // Update the book's available copies
        $book = $borrowing->book;
        $book->returnBook();

        $borrowing->load(['member', 'book']);
        return new BorrowingResource($borrowing);
    }

    public function overdue()
    {
        //update status to overdue
        Borrowing::where('due_date', '<', now())->where('status', 'borrowed')->update(['status' => 'overdue']);

        $overdueBorrowings = Borrowing::with(['member', 'book'])
            ->where('due_date', '<', now())
            ->where('status', 'overdue')
            ->get();

        return BorrowingResource::collection($overdueBorrowings);
    }
}
