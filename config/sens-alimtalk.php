<?php

return [
    /*
    |--------------------------------------------------------------------------
    | NAVER CLOUD PLATFORM API
    |--------------------------------------------------------------------------
    |
    | Go to My Page > Manage Accoutn > Manage Auth Key
    | You can use a previously created authentication key or create a new api authentication key.
    |
    */
    'access_key' => env('NCLOUD_ACCESS_KEY', ''),
    'secret_key' => env('NCLOUD_SECRET_KEY', ''),

    /*
     * Service ID issued when you add a project
     */
    'serviceId' => '',

    /*
     * KakaoTalk Channel ID ((Old) Plus Friend ID)
     */
    'plus_friend_id' => '',
];
