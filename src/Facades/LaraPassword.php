<?php
/**
 * This file is part of LaraPassword.
 * Copyright (c) 2019  Yevhenii Kovalenko
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yekovalenko\LaraPassword\Facades;

use Illuminate\Support\Facades\Facade;
use Yekovalenko\LaraPassword\LaraPasswordManage;

class LaraPassword extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return LaraPasswordManage::class;
    }
}
