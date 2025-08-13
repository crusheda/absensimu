<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = ['user_id', 'token', 'platform', 'os_version', 'model', 'is_rooted'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
