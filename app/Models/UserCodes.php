<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCodes extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user-id',
        'expired',
        'expire_date'
    ];
}
