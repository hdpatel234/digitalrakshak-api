<?php

return [
    'validation' => [
        'email_required' => 'The email field is required.',
        'email_email' => 'Please enter a valid email address.',
        'password_required' => 'The password field is required.',
        'password_string' => 'The password must be a valid text value.',
        'password_min' => 'The password must be at least 6 characters.',
    ],
    'response_messages' => [
        'user_not_found' => 'User not found',
        'login_success' => 'Login successful',
        'invalid_credentials' => 'Invalid email or password',
        'account_inactive' => 'Your account is inactive. Please contact support.',
    ],
];
