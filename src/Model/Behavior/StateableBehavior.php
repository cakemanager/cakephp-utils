<?php

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
        'field'  => 'state',
        'states' => [
            'concept' => 0,
            'active'  => 1,
            'deleted' => -1,
        ],
    ];

    public function stateList() {
        return array_flip($this->config('states'));
    }

    public function findConcept($query, $options) {

        $query->where([
            $this->config('field') => $this->config('states.concept'),
        ]);

        return $query;
    }

    public function findActive($query, $options) {

        $query->where([
            $this->config('field') => $this->config('states.active'),
        ]);

        return $query;
    }

    public function findDeleted($query, $options) {

        $query->where([
            $this->config('field') => $this->config('states.deleted'),
        ]);

        return $query;
    }

}
