<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class profil_rs extends Model
{
    use HasFactory;
    protected $table = 'profil_rs';
    public $timestamps = true;
    use SoftDeletes;
}
