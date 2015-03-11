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
        'table'      => 'metas',
        'foreignKey' => 'model_id',
        'fields'     => [
        ]
    ];

    /*
     * The Table who uses Metas
     *
     */
    protected $Table = null;

    /**
     * The Metas-Table
     *
     * @var type
     */
    protected $TargetTable = null;

    /**
     * Constructor
     *
     * @param Table $table
     * @param array $config
     */
    public function __construct(Table $table, array $config = array())
    {
        parent::__construct($table, $config);

        $this->Table = $table;

        // creating the table
        $this->TargetTable = new Table([
            'alias'      => 'Metas',
            'table'      => $this->config('table'),
            'connection' => $this->Table->connection(),
        ]);

        $this->TargetTable->addBehavior('Timestamp');

        // creating the relation: HasMany
        $this->Table->HasMany('Metas', [
            'foreignKey'  => $this->config('foreignKey'),
            'conditions'  => ['Metas.model' => $this->Table->table()],
            'targetTable' => $this->TargetTable,
        ]);
    }

    /**
     * BeforeFind Callback
     *
     * @param type $event
     * @param type $query
     * @param type $options
     * @param type $primary
     */
    public function beforeFind($event, $query, $options, $primary)
    {


        $query->contain(['Metas' => function ($q) {
                $q->find('list', [
                    'keyField'   => 'name',
                    'valueField' => 'value',
                ]);
                return $q;
            }]);
            }

            /**
             *
             * @param type $event
             * @param type $entity
             * @param type $options
             */
            public function afterSave($event, $entity, $options)
            {
                debug($entity);
                die;
            }

            /**
             *
             * @param type $entity
             * @param type $name
             * @param type $value
             * @return type
             */
            public function setMeta($entity, $name, $value)
            {
                $entity = $this->TargetTable->newEntity([
                    'model'    => $this->Table->table(),
                    'model_id' => $entity->get('id'),
                    'name'     => $name
                ]);

                $query = $this->TargetTable->find()->where([
                    'model'    => $this->Table->table(),
                    'model_id' => $entity->get('id'),
                    'name'     => $name,
                ]);

                if ($query->Count()) {
                    $entity = $query->First();
                }

                $entity->set('value', $value);

                return $this->TargetTable->save($entity);
            }

            /**
             *
             * @param type $entity
             * @param type $name
             * @return boolean
             */
            public function getMeta($entity, $name)
            {

                $query = $this->TargetTable->find()->where([
                    'model'    => $this->Table->table(),
                    'model_id' => $entity->get('id'),
                    'name'     => $name,
                ]);

                if ($query->Count()) {
                    $entity = $query->First();

                    return $entity->get('value');
                }

                return false;
            }

        }
