<?php

namespace Utils\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Utility\Hash;

/**
 * Authorizer component class
 *
 * Component to handle authorization inside your controller.
 *
 * @link http://cakemanager-utils.readthedocs.org/en/latest/components/authorizer
 */
class AuthorizerComponent extends Component
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'roleField' => 'role_id',
    ];

    /**
     * Holder for the Controller
     *
     * @var Controller
     */
    protected $Controller = null;

    /**
     * Holds all current request-info
     *
     * @var type
     */
    protected $_current = [
        'plugin'     => null,
        'controller' => null,
        'action'     => null,
        'roles'      => [],
    ];

    /**
     * Holds the selected action-data
     *
     * @var type
     */
    protected $_selected = [
        'action' => null,
    ];

    /**
     * Data holder
     *
     * @var array
     */
    protected static $_data = [];

    /**
     * Constructor
     *
     * @param ComponentRegistry $registry
     * @param array $config
     */
    public function __construct(ComponentRegistry $registry, array $config = array())
    {
        parent::__construct($registry, $config);

        $this->setController($registry->getController());

        $this->setCurrentParams();
    }

    /**
     * BeforeFilter Event
     *
     * @param type $event
     */
    public function beforeFilter($event)
    {
        $this->setController($event->subject());

        $this->setCurrentParams();
    }

    public function setCurrentParams()
    {
        $this->_current['plugin'] = $this->Controller->request->params['plugin'];
        $this->_current['controller'] = $this->Controller->request->params['controller'];
        $this->_current['action'] = $this->Controller->request->params['action'];

        return $this->_current;
    }

    /**
     * Setter for the Controller [protected]
     * @param type $controller
     */
    public function setController($controller)
    {
        $this->Controller = $controller;
    }

    /**
     * Sets authorization per action.
     * The action variable can be a string or array of strings
     *
     * This method is requested in the controller like:
     *
     * $this->Authorizer->action(["My action"], function($auth) {
     *      // authorization for the chosen actions
     * });
     *
     * @param string|array $actions
     * @param function $function
     */
    public function action($actions, $function)
    {

        if (!is_array($actions)) {
            $actions = [$actions];
        }


        $controller = $this->_current['controller'];

        foreach ($actions as $action) {

            $path = $controller . '.' . $action;

            self::$_data = Hash::insert(self::$_data, $path, [
                        'function'     => $function,
                        'roles'        => [],
            ]);

            $this->_runFunction($action);
        }
    }

    /**
     * This method is used inside the action-method
     * to allow a role to the selected actions
     *
     * Example:
     *
     * $this->Authorizer->action(["My action"], function($auth) {
     *      $auth->allowRole(1);
     * });
     *
     * The role-variable can be an integer or array with integers.
     *
     * @param int|array $roles
     */
    public function allowRole($roles)
    {

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $controller = $this->_current['controller'];
        $action = $this->_selected['action'];

        $path = $controller . '.' . $action . '.roles.';

        foreach ($roles as $role) {
            self::$_data = Hash::insert(self::$_data, $path . $role, true);
        }
    }

    /**
     * This method is used inside the action-method
     * to deny a role from the selected actions
     *
     * Example:
     *
     * $this->Authorizer->action(["My action"], function($auth) {
     *      $auth->denyRole(2);
     * });
     *
     * The role-variable can be an integer or array with integers.
     *
     * @param int|array $roles
     */
    public function denyRole($roles)
    {

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $controller = $this->_current['controller'];
        $action = $this->_selected['action'];

        $path = $controller . '.' . $action . '.roles.';

        foreach ($roles as $role) {
            self::$_data = Hash::insert(self::$_data, $path . $role, false);
        }
    }

    /**
     * This method is used inside the action-method
     * to set a custom boolean to the selected role for the selected action
     *
     * Example:
     *
     * $this->Authorizer->action(["My action"], function($auth) {
     *      $auth->setRole(2, $this->customMethod());
     * });
     *
     * The role-variable can be an integer or array with integers.
     *
     * The value is an boolean.
     *
     * @param int|array $roles
     * @param boolean $value
     */
    public function setRole($roles, $value)
    {

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $controller = $this->_current['controller'];
        $action = $this->_selected['action'];

        $path = $controller . '.' . $action . '.roles.';

        foreach ($roles as $role) {
            self::$_data = Hash::insert(self::$_data, $path . $role, $value);
        }
    }

    /**
     * The final method who will authorize the current request.
     *
     * Use the following in the isAuthorized-method to return if the user is authorized:
     *
     * public function isAuthorized($user) {
     *  // your autorization with the action-method
     *
     *  return $this->Authorizer->authorize();
     * }
     *
     * @return boolean
     */
    public function authorize()
    {

        $user = $this->Controller->Auth->user();
        $role = $user[$this->config('roleField')];

        $controller = $this->_current['controller'];
        $action = $this->_current['action'];

        $path = $controller . '.' . $action . '.roles.' . $role;

        $state = $this->_getState($action, $role);

        return $state;
    }

    /**
     * Getter for the data-array
     *
     * @return type
     */
    public function getData() {
        return self::$_data;
    }

    /**
     * Clear method for the data-array
     */
    public function clearData()
    {
        self::$_data = [];
    }

    /**
     * Checks if the role is allowed to the action.
     * Returns boolean     *
     *
     * @param string $action is the action-name
     * @param integer $role is the role-id
     * @return boolean
     */
    protected function _getState($action, $role)
    {
        $controller = $this->_current['controller'];

        $path = $controller . '.' . $action . '.roles.' . $role;

        $state = Hash::get(self::$_data, $path);

        if ($state == null) {
            $action = '*';
            $path = $controller . '.' . $action . '.roles.' . $role;
            $state = Hash::get(self::$_data, $path);
        }

        if (!is_bool($state)) {
            $state = false;
        }


        return $state;
    }

    /**
     * Runs the given function from the action-method
     *
     * @param type $action
     */
    protected function _runFunction($action)
    {
        $controller = $this->_current['controller'];

        $path = $controller . '.' . $action . '.function';

        $function = Hash::get(self::$_data, $path);

        $this->_selected['action'] = $action;

        if ($function) {
            $function($this, $this->Controller);
        }
    }

}
