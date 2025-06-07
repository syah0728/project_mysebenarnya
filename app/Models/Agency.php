<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    protected $table = 'agency';

    // If you want to allow mass assignment for certain fields, add them here
    // protected $fillable = ['name', ...];

    // Example relationship: An agency has many inquiries
    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'Agency_id', 'id');
    }
}