<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionOverride extends Model
{
    protected $fillable = [
        'proforma_id',
        'application_id',
        'amount',
        'reason',
        'set_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function application()
    {
        return $this->belongsTo(ProformaApplication::class, 'application_id');
    }

    public function setBy()
    {
        return $this->belongsTo(User::class, 'set_by');
    }
}
