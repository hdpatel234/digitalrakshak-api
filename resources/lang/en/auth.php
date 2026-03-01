<?php

return [
    // Login
    'login' => [
        'validation' => [
            'email_required' => 'The email field is required.',
            'email_email' => 'Please enter a valid email address.',
            'password_required' => 'The password field is required.',
            'password_string' => 'The password must be a valid text value.',
            'password_min' => 'The password must be at least 6 characters.',
            'ip_string' => 'IP must be a valid text value.',
            'browser_string' => 'Browser must be a valid text value.',
            'device_string' => 'Device must be a valid text value.',
            'os_string' => 'OS must be a valid text value.',
        ],
        'response_messages' => [
            'user_not_found' => 'User not found',
            'login_success' => 'Login successful',
            'invalid_credentials' => 'Invalid email or password',
            'account_inactive' => 'Your account is inactive. Please contact support.',
        ],
    ],

    // Logout
    'logout' => [
        'validation' => [],
        'response_messages' => [
            'logout_success' => 'Logout successful',
            'logout_all_success' => 'Successfully logged out from all devices',
        ],
    ],

    // Refresh Token
    'refresh_token' => [
        'validation' => [
            'refresh_token_required' => 'Refresh token is required',
            'refresh_token_string' => 'Refresh token must be a string',
            'refresh_token_exists' => 'Invalid refresh token',
        ],
        'response_messages' => [
            'invalid_refresh_token' => 'Invalid refresh token',
            'refresh_success' => 'Token refreshed successfully',
        ],
    ],

    // Get Profile
    'get_profile' => [
        'validation' => [],
        'response_messages' => [
            'profile_success' => 'Profile retrieved successfully',
            'profile_error' => 'Failed to retrieve profile',
        ],
    ],

    // Update Profile
    'update_profile' => [
        'validation' => [
            'first_name_string' => 'First name must be a valid text value.',
            'first_name_max' => 'First name may not be greater than 100 characters.',
            'last_name_string' => 'Last name must be a valid text value.',
            'last_name_max' => 'Last name may not be greater than 100 characters.',
            'email_email' => 'Please enter a valid email address.',
            'email_unique' => 'This email address is already in use.',
            'phone_code_string' => 'Phone code must be a valid text value.',
            'phone_code_max' => 'Phone code may not be greater than 10 characters.',
            'phone_string' => 'Phone must be a valid text value.',
            'phone_max' => 'Phone may not be greater than 20 characters.',
            'phone_unique' => 'This phone number is already in use.',
            'avatar_file' => 'Avatar must be a valid file.',
            'avatar_image' => 'Avatar must be a valid image.',
            'avatar_max' => 'Avatar may not be greater than 5 MB.',
            'remove_logo_boolean' => 'Remove logo must be true or false.',
        ],
        'response_messages' => [
            'profile_updated_success' => 'Profile updated successfully',
        ],
    ],

    // Get Countries
    'get_countries' => [
        'validation' => [],
        'response_messages' => [
            'countries_success' => 'Countries retrieved successfully',
        ]
    ] 
];
