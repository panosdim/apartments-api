<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lessee extends Model
{
    protected $fillable = ['name', 'address', 'postal_code', 'from', 'until', 'flat_id'];
    public $timestamps = false;
    public $table = 'lessees';

    protected $casts = [
        'flat_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(Flat::class);
    }
}
