<?php

namespace Processton\Abacus\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'abacus_currencies';

    protected $fillable = [
        'code',
        'name',
        'symbol',
    ];
}
