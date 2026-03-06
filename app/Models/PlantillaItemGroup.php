<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaItemGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'plantilla_item_id',
        'group_type',
        'group_label',
        'group_total',
        'sort_order',
    ];

    protected $casts = [
        'group_total' => 'decimal:2',
    ];

    public function plantillaItem()
    {
        return $this->belongsTo(PlantillaItem::class);
    }

    public function movements()
    {
        return $this->hasMany(PlantillaItemGroupMovement::class)->orderBy('sort_order');
    }
}
