<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BusCompany extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (BusCompany $busCompany) {
            $busCompany->id = Str::uuid()->toString();
        });
    }

    public $incrementing = false;

    protected $fillable = [
        'name',
        'phone_one',
        'phone_two',
        'email',
        'logo',
        'description',
        'policy',
        'business_licence',
        'status',
    ];

    public function companyImages()
    {
        return $this->hasMany(Image::class, 'source_id');
    }

    public function companyAdmin() {
        return $this->hasOne(User::class);
    }
    public function companyRoutes() {
        return $this->hasMany(Route::class);
    }

}