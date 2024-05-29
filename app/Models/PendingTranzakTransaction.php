<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingTranzakTransaction extends Model
{
    use HasFactory;
    protected $connection = "mysql2";
    protected $fillable = ['id', 'request_id', 'amount', 'currency_code', 'transaction_ref', 'app_id', 'description', 
    'transaction_time', 'payment_type', 'user_type', 'payment_id', 'student_id', 'batch_id', 'unit_id', 'original_amount',
    'reference_number', 'paid_by', 'purpose'];
}
