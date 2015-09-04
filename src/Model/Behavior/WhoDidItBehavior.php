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

use Cake\Core\Configure;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\Utility\Hash;

/**
 * WhoDidIt behavior
 *
 */
class WhoDidItBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     *
     * ### OPTIONS
     * - created_by         string      field to use
     * - modified_by        string      field to use
     * - userModel          string      model to use
     * - fields             array       list of fields to get on query
     *
     */
    protected $_defaultConfig = [
        'created_by' => 'created_by',
        'modified_by' => 'modified_by',
        'createdByPropertyName' => 'createdBy',
        'modifiedByPropertyName' => 'modifiedBy',
        'userModel' => 'Users',
        'contain' => true,
        'fields' => [],
    ];

    /**
     * Holder for table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * Constructor
     *
     * @param \Cake\ORM\Table $table Table who requested the behavior.
     * @param array $config Options.
     */
    public function __construct(Table $table, array $config = [])
    {
        parent::__construct($table, $config);

        $this->Table = $table;

        if ($this->config('created_by')) {
            $this->Table->belongsTo('CreatedBy', [
                'foreignKey' => $this->config('created_by'),
                'className' => $this->config('userModel'),
                'propertyName' => $this->config('createdByPropertyName')
            ]);
        }

        if ($this->config('modified_by')) {
            $this->Table->belongsTo('ModifiedBy', [
                'foreignKey' => $this->config('modified_by'),
                'className' => $this->config('userModel'),
                'propertyName' => $this->config('modifiedByPropertyName')
            ]);
        }
    }

    /**
     * Initialize
     *
     * Initialize callback for Behaviors.
     *
     * @param array $config Options.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
    }

    /**
     * BeforeFind callback
     *
     * Used to add CreatedBy and ModifiedBy to the contain of the query.
     *
     * @param \Cake\Event\Event $event Event.
     * @param \Cake\ORM\Query $query The Query object.
     * @param array $options Options.
     * @param bool $primary Root Query or not.
     * @return void
     */
    public function beforeFind($event, $query, $options, $primary)
    {
        if ($this->config('contain')) {
            if ($this->config('created_by')) {
                $query->contain(['CreatedBy' => ['fields' => $this->config('fields')]]);
            }

            if ($this->config('modified_by')) {
                $query->contain(['ModifiedBy' => ['fields' => $this->config('fields')]]);
            }
        }
    }

    /**
     * BeforeSave callback
     *
     * Used to add the user to the `created_by` and `modified_by` fields.
     *
     * @param \Cake\Event\Event $event Event.
     * @param \Cake\ORM\Entity $entity The Entity to save on.
     * @param array $options Options.
     * @return void
     */
    public function beforeSave($event, $entity, $options)
    {
        $auth = Configure::read('GlobalAuth');
        $id = $auth['id'];

        if ($entity->isNew()) {
            if ($this->config('created_by')) {
                $entity->set($this->config('created_by'), $id);
            }
        }

        if ($this->config('modified_by')) {
            $entity->set($this->config('modified_by'), $id);
        }
    }
}
