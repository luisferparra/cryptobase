<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coins extends Model
{
    use HasFactory;

    protected $fillable = ['coin_cod','symbol','name','is_active'];

    public function datacontrol() {
        return $this->hasOne(DataContol::class);
    }

    public function currentvalue() {
        return $this->hasOne(CoinsCurrentValues::class);
    }
}
