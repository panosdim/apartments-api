<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = ['date', 'amount', 'flat_id', 'comment'];
    protected $casts = ['amount' => 'float', 'flat_id' => 'integer'];
    public $timestamps = false;
    public $table = 'balance';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
