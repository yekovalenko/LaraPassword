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

class BadGeneratorAttributes extends Exception
{
    /**
     * The exception description.
     *
     * @var string
     */
    protected $message = 'The password could not be generated. Try to change the attributes.';
}
