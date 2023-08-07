<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = 'degrees';
    protected $fillable = ['name', 'slug'];

    public function programs()
    {
        # code...
        return $this->belongsToMany(Program::class, DegreeProgram::class);
    }
}
