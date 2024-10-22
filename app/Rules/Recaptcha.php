<?php

namespace App\Rules;

use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\Rule;

class Recaptcha implements Rule
{
    const URL = 'https://www.google.com/recaptcha/api/siteverify';
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $client = new Client([
            'base_uri' => 'https://google.com/recaptcha/api/',
            'timeout' => 2.0
        ]);

        $response = $client->request('POST', 'siteverify', [
            'query' => [
                'secret' => env('GOOGLE_RECAPTCHA_SECRET'),
                'response' => $value
            ]
        ]);

        $body = json_decode((string)$response->getBody());
        if ($body->success) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The recaptcha verification failed. Try again.';
    }

    public static function isInTestMode()
    {
        return Zttp::asFormParams()->post(static::URL, [
            'secret' => config('services.recaptcha.secret'),
            'response' => 'test',
            'remoteip' => request()->ip()
        ])->json()['success'];
    }
}
