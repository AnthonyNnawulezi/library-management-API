<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Borrowing extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowingFactory> */
    use HasFactory;

    protected $fillable = [
        'book_id',
        'member_id',
        'borrowed_date',
        'due_date',
        'returned_date',
        'status',
    ];

    protected $dates = [
        'borrowed_date',
        'due_date',
        'returned_date',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    //if borrowing is overdue
    public function isOverdue()
    {
        return $this->due_date < now() && $this->status === 'borrowed';
        // return $this->due_date < Carbon::today() && $this->status === 'borrowed';
    }
}
