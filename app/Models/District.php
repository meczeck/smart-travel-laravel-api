<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'region_id'
    ];
    public $timestamps = false;
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}