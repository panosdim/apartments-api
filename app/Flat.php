<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flat extends Model
{
    protected $fillable = ['name', 'address', 'floor'];
    public $timestamps  = false;
    public $table       = 'flats';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
