<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmailNotification
{
    protected function verificationUrl($notifiable)
    {
        Log::debug('verificationUrl', [$notifiable]);
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );



        $pattern = '/^https?:\/\/[^\/]+\/api\/v1\//';

        return preg_replace($pattern, config('app.frontend_url'), $signedUrl);
    }
}
