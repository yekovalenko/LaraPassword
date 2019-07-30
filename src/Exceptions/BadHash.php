<?php
/**
 * This file is part of LaraPassword.
 * Copyright (c) 2019  Yevhenii Kovalenko
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yekovalenko\LaraPassword\Exceptions;

use Exception;

class BadHash extends Exception
{
    /**
     * The exception description.
     *
     * @var string
     */
    protected $message = 'Lara Password hash is not set or is incorrect';
}
