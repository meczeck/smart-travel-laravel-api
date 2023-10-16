<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Route extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (Route $busCompany) {
            $busCompany->id = Str::uuid()->toString();
        });
    }
    protected $fillable = [
        'bus_company_id',
        'origin',
        'destination',
        'pathway',
    ];

    public function busCompany()
    {
        return $this->belongsTo(BusCompany::class);
    }
}