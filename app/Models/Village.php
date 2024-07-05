<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'district_id'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function recipients()
    {
        return $this->hasMany(Recipient::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
