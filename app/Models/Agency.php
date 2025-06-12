<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    protected $table = 'agency';

    protected $fillable = [
        'user_id',     // ✅ Add this line
        'username',
        'phone',
        // Add other fields if your table has them
    ];

    // If you want to allow mass assignment for certain fields, add them here
    // protected $fillable = ['name', ...];

    // Example relationship: An agency has many inquiries
    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'Agency_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'Agency_id');
    }

    public static function createWithUser(array $data)
    {
        // 1. Create the user
        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Hash::make($data['password']),
            'role' => 'Agency',
        ]);

        // 2. Create the agency
        $agency = self::create([
            'user_id' => $user->id,
            'username' => $data['username'],      // ✅ required
            'phone' => $data['phone'], 
        ]);

        return $agency;
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }



}