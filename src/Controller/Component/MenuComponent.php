<?php

namespace Utils\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Menu component
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
     * The current area
     * @var type
     */
    protected $area = 'main';

    /**
     * The overall data of the whole menu
     * @var type
     */
    protected static $data = ['main' => []];

    /**
     * The controller
     *
     * @var type
     */
    private $Controller = null;

    public function startup($event) {

        $this->setController($event->subject());
    }

    public function initialize(array $config) {
        parent::initialize($config);

        $this->Controller = $this->_registry->getController();
    }

    public function setController($controller) {
        $this->Controller = $controller;
    }

    /**
     * Method to set or get the current area
     * Leave empty to get the current area
     * Set with a string to set a new area
     *
     * @param type $area
     * @return type
     */
    public function area($area = null) {
        if ($area !== null) {
            $this->area = $area;
        }
        return $this->area;
    }

    /**
     * Returns the menu-data of a specific area, or full data if area is not set
     *
     * @param string $area
     * @return array menu-data
     */
    public function getMenu($area = null) {

        if (!key_exists($area, self::$data)) {
            return self::$data;
        } else {
            return self::$data[$area];
        }
    }

    /**
     * Clears the whole menu
     */
    public function clear() {

        self::$data = ['main' => []];
    }

    /**
     *
     * @param type $title
     * @param type $item
     *
     * ### OPTIONS
     * - id
     * - parent
     * - url
     * - title
     * - icon
     * - area
     * - weight
     */
    public function add($title, $item = array()) {

        $list = self::$data;

        $_item = [
            'id'     => $title,
            'parent' => false,
            'url'    => '#',
            'title'  => $title,
            'icon'   => '',
            'area'   => $this->area(),
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

    public function remove($id, $options = array()) {

        $_options = [
            'area' => false,
        ];

        $options = array_merge($_options, $options);

        if ($options['area']) {
            $this->area = $item['area'];
        }

        unset(self::$data[$this->area][$id]);
    }

    public function beforeRender() {
        $this->Controller->set('menu', self::$data);
    }

}
