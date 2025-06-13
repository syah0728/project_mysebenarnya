<?php
namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function updatePassword($currentPassword, $newPassword)
    {
        if (!Hash::check($currentPassword, $this->password)) {
            return false;
        }

        $this->password = Hash::make($newPassword);
        $this->save();

        return true;
    }

    public function changePassword(Request $request, $user_id)
    {
        $this->authorizeUser($user_id); // Make sure the right user is authorized

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user(); // This should point to the correct User model

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Hash and save the new password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }
    // Add relationships
    public function PublicUser()
    {
        return $this->hasOne(PublicUser::class);
    }

    public function MCMC()
    {
        return $this->hasOne(MCMC::class, 'user_id');
    }

    public function Agency()
    {
        return $this->hasOne(Agency::class);
    }

    // Helper methods
    public function isPublicUser()
    {
        return $this->role === 'PublicUser';
    }

    public function isMCMC()
    {
        return $this->role === 'MCMC';
    }

    public function isAgency()
    {
        return $this->role === 'Agency';
    }
}

