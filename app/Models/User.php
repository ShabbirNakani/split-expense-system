<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPUnit\TextUI\XmlConfiguration\Group;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'number',
        'profile_pic',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    // public function groups()
    // {
    //     return $this->hasMany(GroupList::class, 'user_id', 'id');
    // }

    public function groupUsers()
    {
        return $this->belongsToMany(GroupList::class, 'group_users', 'user_id', 'group_list_id');
    }

    // group expenses
    public function expenses()
    {
        return $this->belongsToMany(Expense::class, 'split_expenses', 'user_id', 'expense_id');
    }
    // for edit group functionality
    public function groupUser()
    {
        return $this->belongsTo(groupUser::class);
    }
    // for edit expense functionality
    public function userRecordsFromPivotInvers()
    {
        return $this->belongsTo(SplitExpense::class);
    }
}
