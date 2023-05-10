<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class GroupList extends Model
{
    use HasFactory, SoftDeletes, Sortable;

    protected $table = 'group_lists';

    protected $fillable = [
        'user_id',
        'title',
        'discription',
        'total_members',
    ];

    // public $sortable = ['id', 'user_id', 'title', 'discription', 'total_members', 'created_at', 'updated_at'];

    // group creator
    public function groupCreator()
    {

        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    // group expenses
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // for my groups
    public function myGroups()
    {
        return $this->hasMany(GroupUser::class, 'group_list_id', 'id');
    }

    // group users for freinds modul =>do not user many to many realationship it doesn't consider deleted_at for pivot table
    // TODO: change this from friends module
    public function users()
    {
        return $this->belongsToMany(User::class, GroupUser::class);
    }

    // for edit group
    public function groupUsers()
    {
        return $this->hasMany(GroupUser::class);
    }
}
