<?php

namespace Utils\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Utility\Hash;

/**
 * Authorizer component
 */
class AuthorizerComponent extends Component
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'roleField'    => 'role_id',
    ];

    /**
     * Holder for the Controller
     * @var Controller
     */
    protected $Controller = null;

    /**
     * Holder for the AuthComponent
     * @var type
     */
    protected $Auth = null;

    /**
     * Holds all current request-info
     * @var type
     */
    protected $_current = [
        'plugin'     => null,
        'controller' => null,
        'action'     => null,
        'roles'      => [],
    ];
    protected $_selected = [
        'action' => null,
    ];

    /**
     * Data holder
     * @var array
     */
    protected static $_data = [];

    /**
     * Constructor
     *
     * @param ComponentRegistry $registry
     * @param array $config
     */
    public function __construct(ComponentRegistry $registry, array $config = array()) {
        parent::__construct($registry, $config);
    }

    /**
     * BeforeFilter Event
     * @param type $event
     */
    public function beforeFilter($event) {
        $this->setController($event->subject());
        $this->Auth = $this->Controller->Auth;

        $this->_current['plugin'] = $this->Controller->request->params['plugin'];
        $this->_current['controller'] = $this->Controller->request->params['controller'];
        $this->_current['action'] = $this->Controller->request->params['action'];
    }

    /**
     * Setter for the Controller [protected]
     * @param type $controller
     */
    public function setController($controller) {
        $this->Controller = $controller;
    }

    public function action($actions, $function) {

        if (!is_array($actions)) {
            $actions = [$actions];
        }


        $controller = $this->_current['controller'];

        foreach ($actions as $action) {

            $path = $controller . '.' . $action;

            self::$_data = Hash::insert(self::$_data, $path, [
                        'function'     => $function,
                        'roles'        => [],
                        'isAuthorized' => false,
            ]);

            $this->_runFunction($action);
        }
    }

    public function allowRole($roles) {

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

    public function denyRole($roles) {

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

    public function setRole($roles, $value) {

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

    public function isAuthorized($bool = true) {

        $controller = $this->_current['controller'];
        $action = $this->_current['action'];

        $path = $controller . '.' . $action . '.isAuthorized';

        if ($bool == false) {
            self::$_data = Hash::insert(self::$_data, $path, false);
            return;
        }

        // check if the IsAuthorized-Component is set
        $behavior = $this->Controller->components()->has('IsAuthorized');

        if ($behavior) {
            self::$_data = Hash::insert(self::$_data, $path, true);
        }
    }

    public function authorize() {

        $user = $this->Auth->user();
        $role = $user[$this->config('roleField')];

        $controller = $this->_current['controller'];
        $action = $this->_current['action'];

        $path = $controller . '.' . $action . '.roles.' . $role;

        $state = $this->_getState($action, $role);

        if ($state) {
            $path = $controller . '.' . $action . '.isAuthorized';
            if (Hash::get(self::$_data, $path)) {
                return $this->Controller->IsAuthorized->authorize();
            }
        }

        return $state;
    }

    protected function _getState($action, $role) {
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

    protected function _runFunction($action) {
        $controller = $this->_current['controller'];

        $path = $controller . '.' . $action . '.function';

        $function = Hash::get(self::$_data, $path);

        $this->_selected['action'] = $action;

        if ($function) {
            $function($this, $this->Controller);
        }
    }

}
