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
namespace Utils\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\TestSuite\TestCase;
use Utils\Controller\Component\MenuComponent;

/**
 * CakeManager\Controller\Component\MenuComponent Test Case
 */
class MenuComponentTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        // Setup our component and fake test controller
        $collection = new ComponentRegistry();
        $this->Menu = new MenuComponent($collection);

        $this->Controller = $this->getMock('Cake\Controller\Controller', ['redirect', 'initMenuItems']);
        $this->Menu->setController($this->Controller);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->Menu->clear();

        unset($this->Menu);

        parent::tearDown();
    }

    /**
     * testBeforeFilter
     *
     * @return void
     */
    public function testBeforeFilter()
    {
        $event = new Event('Controller.beforeFilter', $this->Controller);

        $this->Controller->expects($this->once())->method('initMenuItems');

        $this->Menu->beforeFilter($event);
    }

    /**
     * testArea
     *
     * @return void
     */
    public function testArea()
    {
        // test area starts with main
        $this->assertEquals('main', $this->Menu->area());

        // set custom area
        $this->Menu->area('custom');
        $this->assertEquals('custom', $this->Menu->area(), "Area could not be set");
    }

    /**
     * testActive
     *
     * @return void
     */
    public function testActive()
    {
        // test menu item active
        $this->Menu->add('activeItem');
        $this->Menu->active('activeItem');
        $this->assertTrue($this->Menu->getMenu('main')['activeItem']['active']);

        // test menu item non active
        $this->Menu->add('nonActiveItem');
        $this->assertFalse($this->Menu->getMenu('main')['nonActiveItem']['active']);
    }

    /**
     * testAdd
     *
     * @return void
     */
    public function testAdd()
    {
        // get empty menu
        $empty = $this->Menu->getMenu();

        // test main-area exists
        $this->assertArrayHasKey('main', $empty);
        // tests main-area is empty
        $this->assertEmpty($empty['main']);

        // action
        $this->Menu->add('Test01', []);
        $this->Menu->add('Test02', []);


        // get menu
        $test01 = $this->Menu->getMenu();

        // test main-area exists
        $this->assertArrayHasKey('main', $test01);
        // tests main-area counts 2 items
        $this->assertCount(2, $test01['main']);
    }

    /**
     * testAddFromConfigure
     *
     * @return void
     */
    public function testAddFromConfigure()
    {
        Configure::write('Menu.Register.ConfigureItem0', []);
        Configure::write('Menu.Register.ConfigureItem1', []);
        Configure::write('Menu.Register.ConfigureItem2', []);
        Configure::write('Menu.Register.ConfigureItem3', []);

        $request = new Request();
        $this->Controller = $this->getMock('Cake\Controller\Controller', ['redirect', 'initMenuItems'], [$request]);

        // Setup our component and fake test controller
        $collection = new ComponentRegistry($this->Controller);
        $this->Menu = new MenuComponent($collection);

        $this->Menu->setController($this->Controller);

        $event = new Event('Component.beforeFilter', $this->Controller);

        $this->Menu->beforeFilter($event);

        $test01 = $this->Menu->getMenu();

        // test main-area exists
        $this->assertArrayHasKey('main', $test01);
        // tests main-area counts 4 items
        $this->assertCount(4, $test01['main']);
    }

    /**
     * testClear
     *
     * @return void
     */
    public function testClear()
    {
        $this->Menu->add('Test01', []);
        $this->Menu->add('Test02', []);

        $data = $this->Menu->getMenu();

        $this->assertCount(2, $data['main']);

        $this->Menu->clear();

        $data = $this->Menu->getMenu();

        $this->assertCount(0, $data['main']);
    }

    /**
     * testRemove
     *
     * @return void
     */
    public function testRemove()
    {
        $this->Menu->clear();

        // get empty menu
        $data = $this->Menu->getMenu();

        // tests main-area is empty
        $this->assertEmpty($data['main']);

        // filling
        $this->Menu->add('Test01', []);
        $this->Menu->add('Test02', []);

        // get menu
        $data = $this->Menu->getMenu();

        // tests main-area counts 2 items
        $this->assertCount(2, $data['main']);

        // action
        $this->Menu->remove('Test01');

        // get menu
        $data = $this->Menu->getMenu();

        // tests main-area counts 1 item
        $this->assertCount(1, $data['main']);
    }
}
