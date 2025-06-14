<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $table = 'progress';

    protected $fillable = [
        'Inquiry_id',
        'UpdateDate',
        'ProgressStatus',
        'ProgressDescription',
        'created_at',
        'updated_at',
        'ReviewingOfficer',
        'SupportingDocument'
    ];

    /**
     * Get the inquiry associated with this progress update.
     */
    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'Inquiry_id');
    }
}
