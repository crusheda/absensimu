<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ref_ln extends Model
{
    use HasFactory;
    protected $table = 'referensi_jadwal_libur_nasional';
    public $timestamps = true;
    use SoftDeletes;
}
