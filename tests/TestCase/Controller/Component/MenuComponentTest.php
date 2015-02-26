<?php

namespace Utils\Test\TestCase\Controller\Component;

use Utils\Controller\Component\MenuComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

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
    public function setUp() {
        parent::setUp();

         // Setup our component and fake test controller
        $collection = new ComponentRegistry();
        $this->Menu = new MenuComponent($collection);

        $this->controller = $this->getMock(
            'Cake\Controller\Controller',
            ['redirect']
        );
        $this->Menu->setController($this->controller);

    }

    public function testArea() {

        // test area starts with main
        $this->assertEquals('main', $this->Menu->area());

        // set custom area
        $this->Menu->area('custom');
        $this->assertEquals('custom', $this->Menu->area(), "Area could not be set");
    }

    public function testAdd() {

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

    public function testClear() {

        $data = $this->Menu->getMenu();

        $this->assertCount(2, $data['main']);

        $this->Menu->clear();

        $data = $this->Menu->getMenu();

        $this->assertCount(0, $data['main']);
    }

    public function testRemove() {

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

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown() {
        unset($this->Menu);

        parent::tearDown();
    }

}
