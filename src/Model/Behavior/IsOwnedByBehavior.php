<?php
/**
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
namespace Utils\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;

/**
 * IsOwnedBy behavior
 */
class IsOwnedByBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * ### Options
     * - `column` - Used to use a specific column where the user's id is stored.
     * Default set to `user_id`.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'column' => 'user_id'
    ];

    /**
     * isOwnedBy
     *
     * @param array|\Cake\ORM\Entity $item Entity or array with the object to check on.
     * @param array $user The user who is owner (or not).
     * @return bool
     */
    public function isOwnedBy($item, $user = [])
    {
        if (!is_array($item)) {
            $item = $item->toArray();
        }

        if (empty($user)) {
            return false;
        }

        $itemUserId = $item[$this->config('column')];
        $userId = $user['id'];

        if ($itemUserId === $userId) {
            return true;
        }

        return false;
    }
}
