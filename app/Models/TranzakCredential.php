<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranzakCredential extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';

    protected $table = 'tranzak_credentials';

    protected $fillable = ['api_key', 'app_id', 'cache_token_key', 'cache_token_expiry_key', 'campus_id'];
}
