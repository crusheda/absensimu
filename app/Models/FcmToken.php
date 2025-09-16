<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FcmToken extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'device_id', 'token', 'platform', 'os_version', 'model', 'is_rooted'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
