<?php
/**
 * This file is part of LaraPassword.
 * Copyright (c) 2019  Yevhenii Kovalenko
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yekovalenko\LaraPassword\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LPCategory
 * @package Yekovalenko\LaraPassword\Models
 */
class LPCategory extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];
    /**
     * @var string
     */
    protected $table = 'lp_categories';
}
