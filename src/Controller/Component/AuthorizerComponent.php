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
use Cake\Controller\ComponentRegistry;
use Cake\Utility\Hash;

/**
 * Authorizer component class
 *
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
     * Holder for the Controller.
     *
     * @var \Cake\Controller\Controller
     */
    protected $Controller = null;

    /**
     * Holds all current request-info.
     *
     * @var array
     */
    protected $_current = [
        'plugin' => null,
        'controller' => null,
        'action' => null,
        'roles' => [],
    ];

    /**
     * Holds the selected action-data.
     *
     * @var array
     */
    protected $_selected = [
        'action' => null,
    ];

    /**
     * Data holder.
     *
     * @var array
     */
    protected static $_data = [];

    /**
     * Constructor
     *
     * Constructor for AuthorizerComponent.
     *
     * @param ComponentRegistry $registry ComponentRegistry.
     * @param array $config Configurations.
     * @return void
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);

        $this->setController($registry->getController());

        $this->setCurrentParams();
    }

    /**
     * BeforeFilter Event
     *
     * @param \Cake\Event\Event $event Event.
     * @return void
     */
    public function beforeFilter($event)
    {
        $this->setController($event->subject());

        $this->setCurrentParams();
    }

    /**
     * setCurrentParams
     *
     * Setter for the current propperty.
     *
     * @return array
     */
    public function setCurrentParams()
    {
        $this->_current['plugin'] = $this->Controller->request->params['plugin'];
        $this->_current['controller'] = $this->Controller->request->params['controller'];
        $this->_current['action'] = $this->Controller->request->params['action'];

        return $this->_current;
    }

    /**
     * setController
     *
     * Setter for the Controller.
     *
     * @param type $controller Controller.
     * @return void
     */
    public function setController($controller)
    {
        $this->Controller = $controller;
    }

    /**
     * Sets authorization per action.
     * The action variable can be a string or array of strings
     *
     * ```
     * $this->Authorizer->action(["My action"], function($auth) {
     *      // authorization for the chosen actions
     * });
     * ```
     *
     * @param string|array $actions An array or string to run the function on.
     * @param callable $function Function to authorize with.
     * @return void
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
                        'function' => $function,
                        'roles' => [],
            ]);

            $this->_runFunction($action);
        }
    }

    /**
     * allowRole
     *
     * This method is used inside the action-method
     * to allow a role to the selected actions
     *
     * ```
     * $this->Authorizer->action(["My action"], function($auth) {
     *      $auth->allowRole(1);
     * });
     * ```
     *
     * The role-variable can be an integer or array with integers.
     *
     * @param int|array $roles Array or integer with the roles to allow.
     * @return void
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
     * denyRole
     *
     * This method is used inside the action-method
     * to deny a role from the selected actions
     *
     * ```
     * $this->Authorizer->action(["My action"], function($auth) {
     *      $auth->denyRole(2);
     * });
     * ```
     *
     * The role-variable can be an integer or array with integers.
     *
     * @param int|array $roles Array or integer with the roles to allow.
     * @return void
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
     * setRole
     *
     * This method is used inside the action-method
     * to set a custom boolean to the selected role for the selected action
     *
     * ```
     * $this->Authorizer->action(["My action"], function($auth) {
     *      $auth->setRole(2, $this->customMethod());
     * });
     * ```
     *
     * The role-variable can be an integer or array with integers.
     *
     * The value is an boolean.
     *
     * @param int|array $roles Array or integer with the roles to allow.
     * @param boole $value The value to set to the selected role(s).
     * @return void
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
     * authorize
     *
     * The final method who will authorize the current request.
     *
     * Use the following in the isAuthorized-method to return if the user is authorized:
     *
     * ```
     * public function isAuthorized($user) {
     *  // your autorization with the action-method
     *
     *  return $this->Authorizer->authorize();
     * }
     * ```
     *
     * @return bool
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
     * getData
     *
     * Getter for the data-array
     *
     * @return array
     */
    public function getData()
    {
        return self::$_data;
    }

    /**
     * clearData
     *
     * Clear method for the data-array.
     *
     * @return void
     */
    public function clearData()
    {
        self::$_data = [];
    }

    /**
     * _getState
     *
     * Checks if the role is allowed to the action.
     *
     * @param string $action Is the action-name.
     * @param int $role Is the role-id.
     * @return bool
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
     * _runFunction
     *
     * Runs the given function from the action-method.
     *
     * @param string $action Action name.
     * @return void
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
