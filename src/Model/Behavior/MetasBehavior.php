<?php

namespace Utils\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\Collection\Collection;

/**
 * Metas behavior
 */
class MetasBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'modelAlias' => '',
        'metasModel' => 'Metas',
        'table' => 'metas',
        'foreignKey' => 'rel_id',
        'fields'     => [
        ]
    ];

    /*
     * The Table
     */
    protected $Table = null;

    /**
     * Constructor
     *
     * @param Table $table
     * @param array $config
     */
    public function __construct(Table $table, array $config = array()) {
        parent::__construct($table, $config);

        $this->Table = $table;

        if ($this->config('modelAlias') == null) {
            $this->config('modelAlias', $table->alias());
        }

        $this->Table->HasMany('Metas', [
            'className'  => $this->config('metasModel'),
            'foreignKey' => $this->config('foreignKey'),
            'conditions' => ['Metas.rel_model' => $this->config('modelAlias')],
        ]);

        $this->Table->Metas->table($this->config('table'));
    }

    /**
     * BeforeFind Callback
     *
     * @param type $event
     * @param type $query
     * @param type $options
     * @param type $primary
     */
    public function beforeFind($event, $query, $options, $primary) {
        $query->contain(['Metas']);
    }


    /**
     * This method registers a setter for the model
     *
     * ### Example
     *
     * protected function _setCustom($value) {
     *      $model = TableRegistry::get('Bookmarks');
     *      $this->metas = $model->registerSetter($this, 'custom', $value);
     * }
     *
     * @param type $entity
     * @param type $key
     * @param type $value
     * @return type
     */
    public function registerSetter($entity, $key, $value) {

        if (!$entity->metas) {
            $entity->metas = [];
        }

        if (!$this->_metaKeyExists($entity, $key)) {
            $this->_registerMetaEntity($entity, $key);
        }

        foreach ($entity->metas as $id => $meta) {
            if ($meta->name == $key) {
                $meta->value = $value;
                $entity->metas[$id] = $meta;
            }
        }

        return $entity->metas;
    }

    /**
     * This method regisres a getter for the model
     *
     * ### Example
     *
     * protected function _getCustom() {
     *      $model = TableRegistry::get('Bookmarks');
     *      return $model->registerGetter($this, 'custom');
     * }
     *
     * @param type $entity
     * @param type $key
     * @return type
     */
    public function registerGetter($entity, $key) {

        if (!$this->_metaKeyExists($entity, $key)) {
            return null;
        }

        $metas = new Collection($entity->metas);
        $metaData = $metas->combine('name', 'value')->toArray();

        if (key_exists($key, $metaData)) {
            return $metaData[$key];
        }
        return null;
    }

    /**
     * Checks if an entity has an property 'metas' and the specific name is set too.
     *
     * @param type $entity
     * @param type $name
     * @param type $options
     * @return boolean
     */
    private function _metaKeyExists($entity, $name, $options = []) {

        if (!$entity->metas) {
            return false;
        }
        foreach ($entity->metas as $key => $item) {
            if ($item->name == $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * Creates an Meta-entity and adds it to your entity
     *
     * @param type $entity
     * @param type $field
     * @param type $options
     */
    private function _registerMetaEntity(&$entity, $field, $options = []) {

        $_data = [
            'rel_model' => $entity->source(),
            'name'      => $field,
            'value'     => null,
        ];

        $entity->metas[] = $this->Table->Metas->newEntity($_data);
    }

}
