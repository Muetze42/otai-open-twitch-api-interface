<?php

return [
    'commit' => [
        'user' => [
            'name' => env('GIT_COMMIT_USER_NAME', 'Norman Huth’s Bot'),
            'email' => env('GIT_COMMIT_USER_EMAIL', 'name@example.com'),
        ],
    ],
];
