<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Assignment;
use App\Models\PublicUser;
use App\Models\Mcmc;
use App\Models\Agency;
use App\Models\Progress;
use Illuminate\Support\Facades\DB;


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
    public function assignTo(Agency $agency, $dueDate, $comments)
    {
        try {
            DB::beginTransaction();

            // Update inquiry status
            $this->Agency_id = $agency->id;
            $this->InquiryStatus = 'Assigned';
            $this->save();

            // Create assignment record
            $assignment = Assignment::create([
                'Inquiry_id' => $this->id,
                'Agency_id' => $agency->id,
                'PublicUser_id' => $this->PublicUser_id,
                'AssignmentDate' => now()->toDateString(), // <-- FIXED
                'due_date' => $dueDate,
                'comments' => $comments,
                'AssignmentStatus' => 'Assigned'
            ]);

            DB::commit();
            return $assignment;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Assignment creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function assignToAgency($agencyId, $status = 'Assigned')
    {
        $this->Agency_id = $agencyId;
        $this->InquiryStatus = $status;
        return $this->save();
    }

    public static function getPendingInquiries()
    {
        return static::with('publicUser')
            ->where('InquiryStatus', 'Pending')
            ->orderBy('created_at', 'desc')
            ->get();
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
    public function attachments()
    {
        return $this->hasMany(Attachment::class); // Adjust as needed
    }

public function latestProgress()
{
    return $this->hasOne(Progress::class)->latestOfMany();
}

}
