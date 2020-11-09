<?php
/*/**
 * CakeManager (http://cakemanager.org)
 * Copyright (c) http://cakemanager.org
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) http://cakemanager.org
 * @link          http://cakemanager.org CakeManager Project
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Utils\Database\Type;

use Cake\Database\DriverInterface;
use Cake\Database\Type\BaseType;

class FileType extends BaseType
{
    /**
     * marshal
     *
     * @param  array  $value  Value.
     *
     * @return mixed
     */
    public function marshal($value)
    {
        return $value;
    }

    public function toDatabase($value, DriverInterface $driver)
    {
        return $value;
    }

    public function toPHP($value, DriverInterface $driver)
    {
        return $value;
    }
}
