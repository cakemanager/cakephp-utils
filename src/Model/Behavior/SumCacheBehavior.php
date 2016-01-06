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

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Association;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;

/**
 * SumCache behavior
 *
 * Enables models to cache the sum of a field in a given relation.
 * Very similar to CounterCache.
 *
 * $this->addBehavior('SumCache', [
 *     'Users' => [
 *         'points_count' => ['sumField' => 'points']
 *     ]
 * ]);
 *
 */

class SumCacheBehavior extends Behavior
{

    /**
     * afterSave callback.
     *
     * Makes sure to update sum cache when a new record is created or updated.
     *
     * @param \Cake\Event\Event $event The afterSave event that was fired.
     * @param \Cake\Datasource\EntityInterface $entity The entity that was saved.
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity)
    {
        $this->_processAssociations($event, $entity);
    }

    /**
     * afterDelete callback.
     *
     * Makes sure to update sum cache when a record is deleted.
     *
     * @param \Cake\Event\Event $event The afterDelete event that was fired.
     * @param \Cake\Datasource\EntityInterface $entity The entity that was deleted.
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity)
    {
        $this->_processAssociations($event, $entity);
    }

    /**
     * Iterate all associations and update sum caches.
     *
     * @param \Cake\Event\Event $event Event instance.
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @return void
     */
    protected function _processAssociations(Event $event, EntityInterface $entity)
    {
        foreach ($this->_config as $assoc => $settings) {
            $assoc = $this->_table->association($assoc);
            $this->_processAssociation($event, $entity, $assoc, $settings);
        }
    }

    /**
     * Updates sum cache for a single association
     *
     * @param \Cake\Event\Event $event Event instance.
     * @param \Cake\Datasource\EntityInterface $entity Entity
     * @param Association $assoc The association object
     * @param array $settings The settings for sum cache for this association
     * @return void
     */
    protected function _processAssociation(Event $event, EntityInterface $entity, Association $assoc, array $settings)
    {
        $foreignKeys = (array)$assoc->foreignKey();
        $primaryKeys = (array)$assoc->bindingKey();
        $countConditions = $entity->extract($foreignKeys);
        $updateConditions = array_combine($primaryKeys, $countConditions);
        $countOriginalConditions = $entity->extractOriginalChanged($foreignKeys);

        if ($countOriginalConditions !== []) {
            $updateOriginalConditions = array_combine($primaryKeys, $countOriginalConditions);
        }

        foreach ($settings as $field => $config) {
            if (is_int($field)) {
                $field = $config;
                $config = [];
            }

            if (!is_string($config) && is_callable($config)) {
                $count = $config($event, $entity, $this->_table, false, $countConditions);
            } else {
                $count = $this->_getCount($config, $countConditions);
            }

            $assoc->target()->updateAll([$field => $count], $updateConditions);

            if (isset($updateOriginalConditions)) {
                if (!is_string($config) && is_callable($config)) {
                    $count = $config($event, $entity, $this->_table, true, $countOriginalConditions);
                } else {
                    $count = $this->_getCount($config, $countOriginalConditions);
                }
                $assoc->target()->updateAll([$field => $count], $updateOriginalConditions);
            }
        }
    }

    /**
     * Fetches and returns the sum for a single field in an association
     *
     * @param array $config The sum cache configuration for a single field
     * @param array $conditions Additional conditions given to the query
     * @return int The number of relations matching the given config and conditions
     */
    protected function _getCount(array $config, array $conditions)
    {
        $finder = 'all';
        if (!empty($config['finder'])) {
            $finder = $config['finder'];
            unset($config['finder']);
        }

        if (!isset($config['conditions'])) {
            $config['conditions'] = [];
        }

        if (!isset($config['sumField'])) {
            $config['sumField'] = '';
        }
        $config['conditions'] = array_merge($conditions, $config['conditions']);
        $query = $this->_table->find($finder, $config);

        return $query->sumOf($config['sumField']);
    }
}
