<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicUser extends Model
{
    use HasFactory;

    protected $table = 'publicuser'; // Specify the table name as per your database

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
    ];

    /**
     * Get the user that owns the PublicUser
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'PublicUser_id');
    }




}