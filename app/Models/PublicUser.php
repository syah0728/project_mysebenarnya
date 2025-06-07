<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicUser extends Model
{
    protected $table = 'publicuser'; // because default is plural

    public $timestamps = true;

    protected $fillable = ['id']; // or more if needed

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
