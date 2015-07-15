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
namespace Utils\View\Helper;

use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Menu helper
 *
 * The MenuHelper is used to build menu's. This can be done by calling the `menu`
 * method. The MenuHelper needs other helpers as template for a menu.
 * Per area you are able to set a 'template'.
 *
 */
class MenuHelper extends Helper
{

    /**
     * __construct
     *
     * @param \Cake\View\View $View View.
     * @param array $config configurations.
     */
    public function __construct(\Cake\View\View $View, array $config = [])
    {
        parent::__construct($View, $config);
    }

    /**
     * Helpers
     *
     * @var array
     */
    public $helpers = [
        'Html'
    ];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
    ];

    /**
     * menu
     *
     * The menu method who builds up the menu. This method will return html code.
     * The binded template to an area is used to style the menu.
     *
     * @param string $area Area to build.
     * @param string $helper Helper to use.
     * @param array $options Options.
     * @return string
     */
    public function menu($area, $helper, $options = [])
    {
        $_options = [
        ];

        $options = Hash::merge($_options, $options);

        $builder = $this->_View->helpers()->load($helper);

        $menu = $this->_View->viewVars['menu'][$area];

        $html = '';

        $html .= $builder->beforeMenu($menu);

        foreach ($menu as $item) {
            $html .= $builder->beforeItem($item);
            $html .= $builder->item($item);
            if ($item['children']) {
                $html .= $builder->beforeSubItem($item);
                foreach ($item['children'] as $subItem) {
                    $html .= $builder->subItem($subItem);
                }
                $html .= $builder->afterSubItem($item);
            }
            $html .= $builder->afterItem($item);
        }

        $html .= $builder->afterMenu($menu);

        return $html;
    }
}
