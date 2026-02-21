<?php

return [
    'validation' => [
        'email_required' => 'email_required',
        'email_email' => 'email_invalid',
        'password_required' => 'password_required',
        'password_string' => 'password_string',
        'password_min' => 'password_min',
    ],
    'response_messages' => [
        'login_success' => 'Login successful',
        'invalid_credentials' => 'Invalid email or password',
        'account_inactive' => 'Your account is inactive. Please contact support.',
    ],
];
