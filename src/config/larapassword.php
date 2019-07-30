<?php
/**
 * This file is part of LaraPassword.
 * Copyright (c) 2019  Yevhenii Kovalenko
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Hash
    |--------------------------------------------------------------------------
    |
    | This key is used by the LaraPassword values encrypter and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before using a LaraPassword package!
    |
    */

    'hash' => env('LARAPASSWORD_HASH'),

];
