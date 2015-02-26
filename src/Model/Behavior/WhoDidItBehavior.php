<?php

namespace Utils\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * WhoDidIt behavior
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
        'created_by'  => 'created_by',
        'modified_by' => 'modified_by',
        'userModel'   => 'CakeManager.Users',
        'fields'      => [],
    ];
    protected $Table;

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

        if ($this->config('created_by')) {

            $this->Table->belongsTo('CreatedBy', [
                'foreignKey' => $this->config('created_by'),
                'className'  => $this->config('userModel'),
            ]);
        }

        if ($this->config('modified_by')) {

            $this->Table->belongsTo('ModifiedBy', [
                'foreignKey' => $this->config('modified_by'),
                'className'  => $this->config('userModel'),
            ]);
        }
    }

    /**
     * Initialize
     *
     * @param array $config
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
    }

    /**
     * BeforeFind callback
     *
     * Used to add CreatedBy and ModifiedBy to the contain of the query
     *
     * @param type $event
     * @param type $query
     * @param type $options
     * @param type $primary
     */
    public function beforeFind($event, $query, $options, $primary)
    {

        if ($this->config('created_by')) {

            $query->contain(['CreatedBy' => ['fields' => $this->config('fields')]]);
        }

        if ($this->config('modified_by')) {

            $query->contain(['ModifiedBy' => ['fields' => $this->config('fields')]]);
        }
    }

    public function beforeSave($event, $entity, $options)
    {
        $auth = $_SESSION['Auth'];
        $id = Hash::get($auth, 'User.id');

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
