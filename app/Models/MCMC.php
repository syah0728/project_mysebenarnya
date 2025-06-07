<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MCMC extends Model
{
    public function user() {
        return $this->belongsTo(\App\Models\User::class, 'PublicUser_id');
    }

    public function agency() {
        return $this->belongsTo(\App\Models\Agency::class, 'Agency_id');
    }
}