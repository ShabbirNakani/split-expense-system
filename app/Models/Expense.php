<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'expenses';
    protected $fillable = [
        'title',
        'amount',
        'user_id',
        'group_list_id',
        'expense_date',
        'members',
    ];
    // protected $casts = [
    // 'expense_date' => 'date',
    // ];

    // expense_date getter
    // public function getExpenseDateAttribute($value)
    // {
    //     return $value->format('m-d-Y');
    // }


    public function group()
    {
        return $this->belongsTo(GroupList::class, 'group_list_id', 'id');
    }
    // this will ignore the pivot tables deleted at column
    public function users()
    {
        return $this->belongsToMany(User::class, 'split_expenses', 'user_id', 'expense_id');
    }

    public function splitExpenses()
    {
        return $this->hasMany(SplitExpense::class, 'expense_id', 'id');
    }

    // for edit expense functionality
    public function userRecords()
    {
        return $this->hasMany(SplitExpense::class, 'expense_id', 'id');
    }
}
