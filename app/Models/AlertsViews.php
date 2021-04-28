<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertsViews extends Model
{
    use HasFactory;

    protected $fillable = ['viewed','viewed_at'];

    public function alerts() {
        return $this->belongsTo(Alerts::class);
    }
}
