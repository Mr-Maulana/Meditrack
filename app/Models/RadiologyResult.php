<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadiologyResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'operator_id',
        'doctor_id',
        'image_path',

        'diagnosis',
        'reading_result',
        'status',
        'share_token',
        'sent_via',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function messages()
    {
        return $this->hasMany(RadiologyMessage::class, 'radiology_result_id');
    }

    // Accessor for all image paths as array (handles JSON array or legacy single string)
    public function getImagePathsAttribute()
    {
        if (!$this->image_path) {
            return [];
        }
        
        $decoded = json_decode($this->image_path, true);
        if (is_array($decoded)) {
            return $decoded;
        }
        
        return [$this->image_path];
    }

    // Accessor for full image URLs
    public function getImageUrlsAttribute()
    {
        return array_map(function($path) {
            return asset('storage/' . ltrim($path, '/'));
        }, $this->image_paths);
    }

    // Accessor for full image URL (backward compatibility - returns first image URL)
    public function getImageUrlAttribute()
    {
        $urls = $this->image_urls;
        return !empty($urls) ? $urls[0] : null;
    }
}
