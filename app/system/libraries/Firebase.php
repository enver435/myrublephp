<?php

namespace App\System\Libraries;

use Kreait\Firebase as FirebaseService;
use Kreait\Firebase\ServiceAccount;

class Firebase
{
    private static $firebase;

    public static function init()
    {
        $serviceAccount = ServiceAccount::fromJsonFile('firebase.json');
        $firebase = (new FirebaseService\Factory)
            ->withServiceAccount($serviceAccount)
            ->create();
        self::$firebase = $firebase;

        return new self;
    }

    /**
     * Send Notification
     *
     * @param string $token
     * @param string $title
     * @param string $body
     * @return void
     */
    public function sendNotify($token, $title, $body)
    {
        self::$firebase->getMessaging()->send([
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'android' => [
                'priority' => 'normal',
                'notification' => [
                    'title'      => $title,
                    'body'       => $body,
                    'channel_id' => 'myruble_channel',
                    'sound'      => 'default',
                    'color'      => '#474747'
                ]
            ]
        ]);
    }

    /**
     * Send Multi Notification
     *
     * @param array $token
     * @param string $title
     * @param string $body
     * @return void
     */
    public function sendMultiNotify($tokens, $title, $body)
    {
        self::$firebase->getMessaging()->sendMulticast([
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'android' => [
                'priority' => 'normal',
                'notification' => [
                    'title'      => $title,
                    'body'       => $body,
                    'channel_id' => 'myruble_channel',
                    'sound'      => 'default',
                    'color'      => '#474747'
                ]
            ]
        ], $tokens);
    }
}
