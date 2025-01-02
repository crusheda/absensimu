<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ref_shift extends Model
{
    use HasFactory;
    protected $table = 'referensi_jadwal_shift';
    public $timestamps = true;
    use SoftDeletes;
}
