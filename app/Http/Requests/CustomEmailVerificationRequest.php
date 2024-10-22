<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

class CustomEmailVerificationRequest extends FormRequest
{
    public function authorize()
    {
        $user = User::find($this->route('id'));

        if (! $user || ! hash_equals(sha1($user->getEmailForVerification()), $this->route('hash'))) {
            return false;
        }

        $this->setUserResolver(function () use ($user) {
            return $user;
        });

        return true;
    }

    public function fulfill()
    {
        $user = $this->user();

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new Verified($user));
        }
    }
}
