<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];
    protected static function booted()
    {
        static::deleting(function ($role) {
            $role->menus()->detach(); // hapus pivot
        });
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function menus()
    {
        return $this->belongsToMany(
            Menu::class,
            'menu_role',   // pivot table
            'role_id',     // FK di pivot
            'menu_id'      // FK di pivot
        );
    }
}
