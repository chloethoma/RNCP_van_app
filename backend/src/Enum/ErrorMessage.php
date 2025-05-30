<?php

namespace App\Enum;

enum ErrorMessage: string
{
    // Generic Errors
    case BAD_REQUEST = 'Errors found in received data in %s';
    case INTERNAL_SERVER_ERROR = 'Oops ! Something went wrong.';

    // User Errors
    case USER_UNAUTHENTICATED = 'No authenticated user found in JWT';
    case USER_ACCESS_DENIED = 'You are not authorized to perform this action (user)';
    case USER_CONFLICT = 'User already exists';
    case USER_NOT_FOUND = 'User not found';

    // Spot Errors
    case SPOT_ACCESS_DENIED = 'You are not authorized to perform this action (spot)';
    case SPOT_NOT_FOUND = 'Spot not found';

    // Friendship Errors
    case FRIENDSHIP_BAD_REQUEST = 'Some params sent are not correct or not formatted correctly';
    case FRIENDSHIP_NOT_FOUND = 'Friendship not found';
    case FRIENDSHIP_CONFLICT = 'Friendship already exists';

    // public function code(): int
    // {
    //     return match ($this) {
    //         self::BAD_REQUEST => 1000,

    //         self::USER_UNAUTHENTICATED => 1001,
    //         self::USER_ACCESS_DENIED => 1003,
    //         self::USER_NOT_FOUND => 1004,
    //         self::USER_CONFLICT => 1009,

    //         self::SPOT_ACCESS_DENIED => 2003,
    //         self::SPOT_NOT_FOUND => 2004,

    //         self::FRIENDSHIP_BAD_REQUEST => 3000,
    //         self::FRIENDSHIP_NOT_FOUND => 3004,
    //         self::FRIENDSHIP_CONFLICT => 3009,
    //     };
    // }

    // public function message(): string
    // {
    //     return $this->value;
    // }
}
