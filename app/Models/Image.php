<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Image $image) {
            $image->id = Str::uuid()->toString();
        });
    }

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'url',
        'source_id'
    ];

    public function busCompany()
    {
        return $this->belongsTo(BusCompany::class);
    }
}