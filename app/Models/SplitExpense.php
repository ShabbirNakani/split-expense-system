<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SplitExpense extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'receiver_id',
        'expense_id',
        'group_list_id',
        'amount',
        'status',
        'is_Settled',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expens_id', 'id');
    }
    // invers relation for user records for edit expense
    public function userRecordsInverse()
    {
        return $this->belongsTo(Expense::class);
    }
    // this too for edit =>chaining through table
    public function userRecordsFromPivot()
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }
}
