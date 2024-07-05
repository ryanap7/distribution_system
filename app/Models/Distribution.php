<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory;

    protected $fillable = ['recipient_id', 'stage', 'recipient_photo', 'date', 'amount', 'notes'];

    public function recipient()
    {
        return $this->belongsTo(Recipient::class, 'recipient_id');
    }
}
