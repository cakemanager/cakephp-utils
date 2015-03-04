<?php

namespace Utils\Test\TestCase\Model\Behavior;

use CakeManager\Model\Behavior\StateableBehavior;
use Cake\TestSuite\TestCase;

/**
 * CakeManager\Model\Behavior\StateableBehavior Test Case
 */
class StateableBehaviorTest extends TestCase
{

    public $fixtures = ['plugin.utils.articles'];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();

        $this->Model = \Cake\ORM\TableRegistry::get('Articles');
    }

    public function testLoadingBehavior() {

        $this->assertFalse($this->Model->behaviors()->has('Stateable'));

        $this->Model->addBehavior('Utils.Stateable');

        $this->assertTrue($this->Model->behaviors()->has('Stateable'));
    }

    public function testStateList() {

        $_list = [
            'concept' => 0,
            'active'  => 1,
            'deleted' => -1,
        ];

        $this->assertEquals($_list, $this->Model->behaviors()->get('Stateable')->config('states'));

        $_list = [
            0  => 'concept',
            1  => 'active',
            -1 => 'deleted',
        ];

        $this->assertEquals($_list, $this->Model->stateList());
    }

    public function testFindConcept() {

        $data = $this->Model->find('concept')->toArray();

        $this->assertEquals(2, $data[0]['id']);
        $this->assertEquals("Second Article", $data[0]['title']);
    }

    public function testFindActive() {

        $data = $this->Model->find('active')->toArray();

        $this->assertEquals(1, $data[0]['id']);
        $this->assertEquals("First Article", $data[0]['title']);
    }

    public function testFindDeleted() {

        $data = $this->Model->find('deleted')->toArray();

        $this->assertEquals(3, $data[0]['id']);
        $this->assertEquals("Third Article", $data[0]['title']);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown() {
        unset($this->Stateable);

        parent::tearDown();
    }

}
