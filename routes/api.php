<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BorrowingController;
use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('authors', AuthorController::class);

//books
Route::apiResource('books', BookController::class);

//members
Route::apiResource('members', MemberController::class);

//borrowings
Route::apiResource('borrowings', BorrowingController::class)->only(['index', 'store', 'show']);
// Route::apiResource('borrowings', BorrowingController::class)->except(['update', 'destroy']);

//return and overdue
Route::post('borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook']);
Route::get('borrowings/overdue/list', [BorrowingController::class, 'overdue']);

//dashboard stats
Route::get('statistics', function () {
    return response()->json([
        'total_books' => Book::count(),
        'total_members' => Member::count(),
        'total_borrowings' => Borrowing::count(),
        'active_borrowings' => Borrowing::where('status', 'borrowed')->count(),
    ]);
});
