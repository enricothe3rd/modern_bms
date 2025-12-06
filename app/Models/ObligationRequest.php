<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObligationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'obr_number',
        'department_id',
        'claimant_payee_id',
        'obligation_date',
        'particulars',
        'optional_field_1',
        'optional_field_2',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'obligation_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function claimantPayee()
    {
        return $this->belongsTo(ClaimantPayee::class);
    }

    public function signatories()
    {
        return $this->hasMany(ObligationRequestSignatory::class);
    }

    public function items()
    {
        return $this->hasMany(ObligationRequestItem::class)->orderBy('order');
    }

    public function signatory1()
    {
        return $this->hasOne(ObligationRequestSignatory::class)->where('signatory_type', 'signatory_1');
    }

    public function signatory2()
    {
        return $this->hasOne(ObligationRequestSignatory::class)->where('signatory_type', 'signatory_2');
    }

    public function notedSignatory()
    {
        return $this->hasOne(ObligationRequestSignatory::class)->where('signatory_type', 'noted');
    }
}
