<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OtpCode extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (OtpCode $otpCode) {
            $otpCode->id = Str::uuid()->toString();
        });
    }

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'otp',
        'expire_at'
    ];



}