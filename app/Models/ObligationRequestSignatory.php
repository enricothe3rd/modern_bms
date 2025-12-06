<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObligationRequestSignatory extends Model
{
    use HasFactory;

    protected $fillable = [
        'obligation_request_id',
        'user_id',
        'signatory_type',
        'signatory_date',
        'order',
    ];

    protected $casts = [
        'signatory_date' => 'date',
    ];

    public function obligationRequest()
    {
        return $this->belongsTo(ObligationRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
