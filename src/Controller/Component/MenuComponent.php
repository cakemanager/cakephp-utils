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
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Menu component.
 *
 */
class MenuComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * The current area.
     *
     * @var string
     */
    protected $area = 'main';

    /**
     * The overall data of the whole menu.
     *
     * @var array
     */
    protected static $data = ['main' => []];

    /**
     * The controller.
     *
     * @var \Cake\Controller\Controller
     */
    private $Controller = null;

    /**
     * startup.
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
     * initialize
     *
     * Initialize callback for Components.
     *
     * @param array $config Configurations.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->Controller = $this->_registry->getController();
    }

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
     * BeforeFilter Event
     *
     * This method will check if the `initMenuItems`-method exists in the
     * `AppController`. That method contains menu-items to add.
     *
     * @param \Cake\Event\Event $event Event.
     * @return void
     */
    public function beforeFilter($event)
    {
        $this->setController($event->subject());

        if (method_exists($this->Controller, 'initMenuItems')) {
            $this->Controller->initMenuItems($event);
        }
    }

    /**
     * area
     *
     * Method to set or get the current area.
     *
     * Leave empty to get the current area.
     *
     * Set with a string to set a new area.
     *
     * @param string|void $area The area where the item should be stored.
     * @return string
     */
    public function area($area = null)
    {
        if ($area !== null) {
            $this->area = $area;
        }
        return $this->area;
    }

    /**
     * getMenu
     *
     * Returns the menu-data of a specific area, or full data if area is not set.
     *
     * @param string $area The area where the item should be stored.
     * @return array The menu-items of the area.
     */
    public function getMenu($area = null)
    {
        if (!key_exists($area, self::$data)) {
            return self::$data;
        } else {
            return self::$data[$area];
        }
    }

    /**
     * clear
     *
     * Clears the menu-data property.
     *
     * @return void
     */
    public function clear()
    {
        self::$data = ['main' => []];
    }

    /**
     * add
     *
     * Adds a new menu-item.
     *
     * ### OPTIONS
     * - id
     * - parent
     * - url
     * - title
     * - icon
     * - area
     * - weight
     *
     * @param string $title The title or id of the item.
     * @param array $item Options for the item.
     * @return void
     */
    public function add($title, $item = [])
    {
        $list = self::$data;

        $_item = [
            'id' => $title,
            'parent' => false,
            'url' => '#',
            'title' => $title,
            'icon' => '',
            'area' => $this->area(),
            'active' => false,
            'weight' => 10,
            ''
        ];

        $item = array_merge($_item, $item);

        $url = Router::url($item['url']);

        if ($url === "/" . $this->Controller->request->url) {
            $item['active'] = true;
        }

        $this->area = $item['area'];

        $data = self::$data;

        $data[$this->area][$item['id']] = $item;

        $data[$this->area] = Hash::sort($data[$this->area], '{s}.weight', 'asc');

        self::$data = $data;
    }

    /**
     * remove
     *
     * Removes a menu-item.
     *
     * ### OPTIONS
     * - area       The area to remove from.
     *
     * @param string $id Idenitifier of the item.
     * @param array $options Options.
     * @return void
     */
    public function remove($id, $options = [])
    {
        $_options = [
            'area' => false,
        ];

        $options = array_merge($_options, $options);

        if ($options['area']) {
            $this->area = $item['area'];
        }

        unset(self::$data[$this->area][$id]);
    }

    /**
     * beforeRender
     *
     * beforeRender callback for Components
     *
     * @return void
     */
    public function beforeRender()
    {
        $this->Controller->set('menu', self::$data);
    }
}
