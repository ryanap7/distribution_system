<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'nik', 'village_id', 'ktp_photo'];

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function distributionRecords()
    {
        return $this->hasMany(Distribution::class);
    }
}
