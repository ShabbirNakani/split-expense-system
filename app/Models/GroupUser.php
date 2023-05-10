<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'group_users';
    protected $fillable = [
        'user_id',
        'group_list_id',
    ];

    // to show groups they belong => for each user
    // public function myGroups()
    // {
    //     return $this->belongsTo(GroupList::class, 'group_list_id', 'id');
    // }

    public function users()
    {
        return $this->belongsTo(GroupList::class);
    }

    // for edit group
    public function groupUsers()
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }
}
