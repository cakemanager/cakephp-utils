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
namespace Utils\Controller\Component;

use Cake\Controller\Component;
use Cake\Utility\Hash;

/**
 * Search component
 */
class SearchComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'filters' => [
        ],
        '_default' => [
            'field' => '',
            'column' => '',
            'operator' => 'LIKE',
            'attributes' => [
                'label' => false,
                'type' => 'text',
                'placeholder' => null,
            ],
            'options' => false
        ]
    ];

    /**
     * The controller.
     *
     * @var \Cake\Controller\Controller
     */
    private $Controller = null;

    /**
     * setController
     *
     * Setter for the Controller property.
     *
     * @param \Cake\Controller\Controller $controller Controller.
     * @return void
     */
    public function setController($controller)
    {
        $this->Controller = $controller;
    }

    /**
     * startup
     *
     * Startup callback for Components.
     *
     * @param \Cake\Event\Event $event Event.
     * @return void
     */
    public function startup($event)
    {
        $this->setController($event->subject());
    }

    /**
     * beforeRender
     *
     * beforeRender callback for Component.
     *
     * @param \Cake\Event\Event $event Event.
     * @return void
     */
    public function beforeRender($event)
    {
        $this->Controller->set('searchFilters', $this->_normalize($this->config('filters')));
    }

    /**
     * addFilter
     *
     * Adds a filter to the Search Component.
     *
     * ### Options:
     * - field      Field to use.
     * - column     Column to use from the table.
     * - operator   The operator to use like: 'Like' or '='.
     * - options    List for a select-box.
     * - attributes Attributes for the input-field.
     *
     * @param string $name Name of the filter.
     * @param array $options Options.
     * @return void
     */
    public function addFilter($name, $options = [])
    {
        $_options = $this->config('_default');

        $_options['field'] = $name;
        $_options['column'] = $name;

        $options = array_merge($_options, $options);

        $this->config('filters.' . $name, $options, true);
    }

    /**
     * removeFilter
     *
     * Removes an filter.
     *
     * @param string $name Name of the filter.
     * @return void
     */
    public function removeFilter($name)
    {
        $filters = $this->config('filters');
        unset($filters[$name]);

        $this->config('filters', $filters, false);
    }

    /**
     * Initialize
     *
     * @param array $config Options.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
    }

    /**
     * Search
     *
     * The search-method itself. Needs a Query-object, adds filters and returns the Query-object.
     *
     * @param \Cake\ORM\Query $query Query Object.
     * @param type $options Options.
     * @return \Cake\ORM\Query
     */
    public function search(\Cake\ORM\Query $query, $options = [])
    {
        $_query = $this->Controller->request->query;
        $this->Controller->request->data = $_query;

        $params = $_query;
        $filters = $this->_normalize($this->config('filters'));

        foreach ($filters as $field => $options) {
            $hash = Hash::get($params, $options['column']);
            if (!empty($hash)) {
                $key = $this->_buildKey($field, $options);
                $value = $this->_buildValue($field, $options, $params);

                $query->where([$key => $value]);

                $this->_setValue($field, $options, $params);
            }
        }

        return $query;
    }

    /**
     * _buildKey
     *
     * Builds the key-side of the `where()`-method.
     * Inlcuding the operators like `Like` and `=`.
     *
     * @param string $field The fieldname.
     * @param array $options Options of the field.
     * @return string
     */
    protected function _buildKey($field, $options)
    {
        $string = $options['column'];

        // if the operator is `LIKE`
        if ($options['operator'] === 'LIKE') {
            $string .= ' LIKE';
        }
        return $string;
    }

    /**
     * _buildKey
     *
     * Builds the value-side of the `where()`-method.
     *
     * @param string $field The fieldname.
     * @param array $options Options of the field.
     * @param array $params Parameters.
     * @return string
     */
    protected function _buildValue($field, $options, $params)
    {
        $string = null;

        if ($options['operator'] === 'LIKE') {
            $string .= '%';
        }
        $string .= Hash::get($params, $options['column']);
        if ($options['operator'] === 'LIKE') {
            $string .= '%';
        }
        return $string;
    }

    /**
     * _setValue
     *
     * Sets the value to the current filter.
     *
     * @param string $field The fieldname.
     * @param array $options Options of the field.
     * @param type $params Parameters.
     * @return void
     */
    protected function _setValue($field, $options, $params)
    {
        $key = 'filters.' . $field . '.attributes.value';
        $value = Hash::get($params, $options['column']);

        $this->config($key, $value);
    }

    /**
     * _normalize
     *
     * Normalizes the filters-array. This can be helpfull to use automated settings.
     *
     * @param array $filters List of filters.
     * @param array $options Options
     * @return array
     */
    protected function _normalize($filters, $options = [])
    {
        foreach ($filters as $key => $filter) {
            if ($filter['options']) {
                $filter['operator'] = '=';
                $filter['attributes']['empty'] = true;
            }
            if (is_null($filter['attributes']['placeholder'])) {
                $filter['attributes']['placeholder'] = $filter['column'];
            }

            $filters[$key] = $filter;
        }

        return $filters;
    }
}
