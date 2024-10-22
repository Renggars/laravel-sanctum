<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logging extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'message',
        'action',
    ];

    public static function record($user = null, $message)
    {
        return static::create([
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'message' => $message,
            'action' => request()->method() . " " . request()->fullUrl(),
        ]);
    }
}
