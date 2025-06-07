<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    // Explicit table name (optional if Laravel's naming doesn't match)
    protected $table = 'inquiry';

    // Fields that are mass assignable
    protected $fillable = [
        'PublicUser_id',
        'MCMC_id',
        'Agency_id',
        'NewsTitle',
        'NewsContent',
        'NewsSource',
        'InquiryDate',
        'InquiryStatus',
        'VerificationStatus',
        'attachment',
    ];

    // Optional: date casting
    protected $dates = [
        'InquiryDate',
        'created_at',
        'updated_at',
    ];
    //view the inquiry history for a public user
    public static function findByUser($inquiryId, $userId)
    {
        return self::where('id', $inquiryId)
                    ->where('PublicUser_id', $userId)
                    ->first();
    }


    public static function getByPublicUser($user_id)
    {
        return self::where('PublicUser_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function dashboard($user_id)
    {
        $user = auth()->user();

        if (!$user || $user->id != $user_id || !$user->isPublicUser()) {
            abort(403, 'Unauthorized action.');
        }

        $total = Inquiry::countByUser($user->id);
        $pending = Inquiry::countByStatus($user->id, 'Pending');
        $inProgress = Inquiry::countByStatus($user->id, 'In Progress');
        $resolved = Inquiry::countByStatus($user->id, 'Resolved');
        $recent = Inquiry::recentByUser($user->id, 5);

        return view('PublicUser.dashboard', compact('total', 'pending', 'inProgress', 'resolved', 'recent'));
    }


    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'PublicUser_id');
    }
    

    public static function countByUser($user_id)
    {
        return static::where('PublicUser_id', $user_id)->count();
    }

    public static function countByStatus($user_id, $status)
    {
        return static::where('PublicUser_id', $user_id)
            ->where('InquiryStatus', $status)
            ->count();
    }

    public static function recentByUser($user_id, $limit = 5)
    {
        return static::where('PublicUser_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    //show agency handling the inquiry
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'Agency_id');
    }
    

    // Scopes for filtering inquiries
    public function scopeUnassigned($query)
    {
        return $query->whereNull('Agency_id');
    }

    public function scopeAssigned($query)
    {
        return $query->whereNotNull('Agency_id');
    }

    // Business logic for assigning inquiry
    public function assignTo(Agency $agency)
    {
        $this->Agency_id = $agency->id;
        $this->InquiryStatus = 'Assigned';
        $this->save();

        // Create assignment record
        return Assignment::create([
            'Inquiry_id' => $this->id,
            'Agency_id' => $agency->id,
            'PublicUser_id' => $this->PublicUser_id,
            'AssignmentDate' => now(),
            'AssignmentStatus' => 'Assigned',
        ]);
    }

    // Stats methods
    public static function getStats()
    {
        return [
            'total' => self::count(),
            'pending' => self::where('InquiryStatus', 'Pending')->count(),
            'inProgress' => self::where('InquiryStatus', 'In Progress')->count(),
            'resolved' => self::where('InquiryStatus', 'Resolved')->count(),
        ];
    }
    // Relationships

    public function publicUser()
    {
        return $this->belongsTo(PublicUser::class, 'PublicUser_id');
    }

    public function mcmc()
    {
        return $this->belongsTo(Mcmc::class, 'MCMC_id');
    }


    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'Inquiry_id');
    }

    public function progressUpdates()
    {
        return $this->hasMany(Progress::class, 'Inquiry_id');
    }
}
