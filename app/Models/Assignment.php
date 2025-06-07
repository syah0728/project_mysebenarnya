<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $table = 'assignment'; // match your DB table name exactly

    protected $fillable = [
        'Inquiry_id',
        'Agency_id',
        'PublicUser_id',
        'AssignmentDate',
        'AssignmentStatus',
    ];

    // Relationships (optional but good for future use)
    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'Inquiry_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'Agency_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'PublicUser_id');
    }
}
