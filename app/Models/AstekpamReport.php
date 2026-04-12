<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AstekpamReport extends Model
{
    //
    protected $guarded = [];
protected $casts = [
    'personil' => 'array',
    'tugas' => 'array',
];
}
