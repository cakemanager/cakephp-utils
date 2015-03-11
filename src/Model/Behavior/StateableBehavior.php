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
 * Stateable behavior
 */
class StateableBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     *
     * ### OPTIONS
     * - field          string      the column where the state is saved
     * - states         array       list of states (key) and its integer (value)
     *
     */
    protected $_defaultConfig = [
        'field' => 'state',
        'states' => [
            'concept' => 0,
            'active' => 1,
            'deleted' => -1,
        ],
    ];

    /**
     * stateList
     *
     * Returns a list of states. Can be used for the FormHelper.
     *
     * @return array
     */
    public function stateList()
    {
        return array_flip($this->config('states'));
    }

    /**
     * findConcept
     *
     * Finder for the state 'concepts'.
     *
     * @param \Cake\ORM\Query $query The current Query object.
     * @param array $options Optional options.
     * @return \Cake\ORM\Query The modified Query object.
     */
    public function findConcept($query, $options)
    {
        $query->where([
            $this->config('field') => $this->config('states.concept'),
        ]);

        return $query;
    }

    /**
     * findActive
     *
     * Finder for the state 'active'.
     *
     * @param \Cake\ORM\Query $query The current Query object.
     * @param array $options Optional options.
     * @return \Cake\ORM\Query The modified Query object.
     */
    public function findActive($query, $options)
    {
        $query->where([
            $this->config('field') => $this->config('states.active'),
        ]);

        return $query;
    }

    /**
     * findDeleted
     *
     * Finder for the state 'deleted'.
     *
     * @param \Cake\ORM\Query $query The current Query object.
     * @param array $options Optional options.
     * @return \Cake\ORM\Query The modified Query object.
     */
    public function findDeleted($query, $options)
    {
        $query->where([
            $this->config('field') => $this->config('states.deleted'),
        ]);

        return $query;
    }
}
