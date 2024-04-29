<?php

namespace App\Models;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getCoordinatorNameAttribute()
    {
        $coordinator = $this->users()
            ->where('role', 'Coordinator')
            ->first();

        return $coordinator ? $coordinator->name : null;
    }
}