<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Assignment;
use App\Models\Inquiry;
use App\Models\Agency;
use App\Models\User;
use App\Models\PublicUser;
use App\Models\Mcmc;


class Assignment extends Model
{
    use HasFactory;

    protected $table = 'assignment'; // match your DB table name exactly

    protected $fillable = [
        'Inquiry_id',
        'Agency_id',
        'PublicUser_id',
        'AssignmentDate',
        'due_date',
        'comments',
        'AssignmentStatus'
    ];

    protected $dates = [
        'AssignmentDate',
        'due_date',
        'created_at',
        'updated_at'
    ];

    public static function createNewAssignment($data)
    {
        return static::create([
            'Inquiry_id' => $data['inquiry_id'],
            'Agency_id' => $data['agency_id'],
            'PublicUser_id' => $data['public_user_id'],
            'AssignmentDate' => now()->toDateString(),
            'due_date' => $data['due_date'],
            'comments' => $data['comments'],
            'AssignmentStatus' => 'Assigned'
        ]);
    }

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
