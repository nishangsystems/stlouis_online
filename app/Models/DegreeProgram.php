<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeProgram extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'degree_programs';
    protected $fillable = [ 'program_id','degree_id'];

}
