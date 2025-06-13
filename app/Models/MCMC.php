<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MCMC extends Model
{
    protected $table = 'mcmc';
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function agency() {
        return $this->belongsTo(\App\Models\Agency::class, 'Agency_id');
    }
}