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

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Utils\Model\Behavior\IsOwnedByBehavior;

/**
 * Utils\Model\Behavior\IsOwnedByBehavior Test Case
 */
class IsOwnedByBehaviorTest extends TestCase
{

    public $fixtures = ['plugin.Utils.Articles'];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->Articles->removeBehavior('IsOwnedBy');
        unset($this->Articles);

        parent::tearDown();
    }

    /**
     * Test IsOwnedBy Method with entity
     *
     * @return void
     */
    public function testIsOwnedByEntity()
    {
        $this->Articles = TableRegistry::get('Articles');

        $this->Articles->addBehavior('Utils.IsOwnedBy');

        $user1 = [
            'id' => 1,
        ];

        $user2 = [
            'id' => 3,
        ];

        $item = $this->Articles->get(1);
        $this->assertEquals(true, $this->Articles->isOwnedBy($item, $user1));

        $item = $this->Articles->get(2);
        $this->assertEquals(false, $this->Articles->isOwnedBy($item, $user1));
        $this->assertEquals(true, $this->Articles->isOwnedBy($item, $user2));
    }

    /**
     * Test IsOwnedBy Method with array
     *
     * @return void
     */
    public function testIsOwnedByArray()
    {
        $this->Articles = TableRegistry::get('Articles');

        $this->Articles->addBehavior('Utils.IsOwnedBy');

        $user1 = [
            'id' => 1,
        ];

        $user2 = [
            'id' => 3,
        ];

        $item = $this->Articles->get(1)->toArray();
        $this->assertEquals(true, $this->Articles->isOwnedBy($item, $user1));

        $item = $this->Articles->get(2)->toArray();
        $this->assertEquals(false, $this->Articles->isOwnedBy($item, $user1));
        $this->assertEquals(true, $this->Articles->isOwnedBy($item, $user2));
    }

    /**
     * Test IsOwnedBy Method without user
     *
     * @return void
     */
    public function testIsOwnedByWithoutUser()
    {
        $this->Articles = TableRegistry::get('Articles');

        $this->Articles->addBehavior('Utils.IsOwnedBy');

        $user1 = [
            'id' => 1,
        ];

        $user2 = [
            'id' => 3,
        ];

        $item = $this->Articles->get(1)->toArray();
        $this->assertEquals(false, $this->Articles->isOwnedBy($item));

        $item = $this->Articles->get(2)->toArray();
        $this->assertEquals(false, $this->Articles->isOwnedBy($item));
        $this->assertEquals(false, $this->Articles->isOwnedBy($item));
    }

    /**
     * Test IsOwnedBy Method different column
     *
     * @return void
     */
    public function testIsOwnedByConfigColumn()
    {
        $this->Articles = TableRegistry::get('Articles');

        $this->Articles->addBehavior('Utils.IsOwnedBy', [
            'column' => 'created_by'
        ]);

        $user1 = [
            'id' => 1,
        ];

        $user2 = [
            'id' => 3,
        ];

        $item = $this->Articles->get(1)->toArray();
        $this->assertEquals(true, $this->Articles->isOwnedBy($item, $user1));
        $this->assertEquals(false, $this->Articles->isOwnedBy($item, $user2));

        $item = $this->Articles->get(2)->toArray();
        $this->assertEquals(true, $this->Articles->isOwnedBy($item, $user1));
        $this->assertEquals(false, $this->Articles->isOwnedBy($item, $user2));
    }
}
