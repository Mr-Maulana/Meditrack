<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadiologyMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'radiology_result_id',
        'sender_type',
        'sender_id',
        'channel',
        'message_text',
        'attachment_path',
    ];

    public function radiologyResult()
    {
        return $this->belongsTo(RadiologyResult::class, 'radiology_result_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
