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
namespace Utils\Test\TestCase\Model\Behavior;

use CakeManager\Model\Behavior\StateableBehavior;
use Cake\TestSuite\TestCase;

/**
 * CakeManager\Model\Behavior\StateableBehavior Test Case
 *
 */
class StateableBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = ['plugin.utils.articles'];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Model = \Cake\ORM\TableRegistry::get('Articles');
    }

    /**
     * testLoadingBehavior
     *
     * @return void
     */
    public function testLoadingBehavior()
    {
        $this->assertFalse($this->Model->behaviors()->has('Stateable'));

        $this->Model->addBehavior('Utils.Stateable');

        $this->assertTrue($this->Model->behaviors()->has('Stateable'));
    }

    /**
     * testStateList
     *
     * @return void
     */
    public function testStateList()
    {
        $_list = [
            'concept' => 0,
            'active' => 1,
            'deleted' => -1,
        ];

        $this->assertEquals($_list, $this->Model->behaviors()->get('Stateable')->config('states'));

        $_list = [
            0 => 'concept',
            1 => 'active',
            -1 => 'deleted',
        ];

        $this->assertEquals($_list, $this->Model->stateList());
    }

    /**
     * testFindConcept
     *
     * @return void
     */
    public function testFindConcept()
    {
        $data = $this->Model->find('concept')->toArray();

        $this->assertEquals(2, $data[0]['id']);
        $this->assertEquals("Second Article", $data[0]['title']);
    }

    /**
     * testFindActive
     *
     * @return void
     */
    public function testFindActive()
    {
        $data = $this->Model->find('active')->toArray();

        $this->assertEquals(1, $data[0]['id']);
        $this->assertEquals("First Article", $data[0]['title']);
    }

    /**
     * testFindDeleted
     *
     * @return void
     */
    public function testFindDeleted()
    {
        $data = $this->Model->find('deleted')->toArray();

        $this->assertEquals(3, $data[0]['id']);
        $this->assertEquals("Third Article", $data[0]['title']);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Stateable);

        parent::tearDown();
    }
}
