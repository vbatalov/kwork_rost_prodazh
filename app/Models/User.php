<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "cid",
        "first_name",
        "last_name",
        "phone",
        "quota",
        "email",
    ];


    /**
     * Убрать одну квоту у пользователя
     * @return void
     * @var User $user
     */
    public function takeAwayOneQuota(): void
    {
        $current_quota = auth()->user()->quota;

        if ($current_quota >= 1) {
            auth()->user()->update(["quota" => $current_quota - 1]);
        }
    }

}
