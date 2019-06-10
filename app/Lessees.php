<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lessees extends Model
{
    protected $fillable = ['name', 'address', 'postal_code', 'from', 'until','flat_id'];
    public $timestamps  = false;
    public $table       = 'lessees';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
