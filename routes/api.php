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
use App\Models\Author;
use App\Http\Controllers\AuthController;


// Route::get('/user', function (Request $request) {or the user route below
//     return $request->user();
// })->middleware('auth:sanctum');

//authntication routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
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
                // 'total_authors' => Book::distinct('author_id')->count('author_id'),
                'total_authors' => Author::count(),
                'total_members' => Member::count(),
                'total_borrowings' => Borrowing::count(),
                'overdue_borrowings' => Borrowing::where('status', 'overdue')->count(),
            ]);
        });
    });

    //recent books
    Route::prefix('v2')->group(function () {
        Route::get('recent-books', [BookController::class, 'listBooks']);
    });
});
