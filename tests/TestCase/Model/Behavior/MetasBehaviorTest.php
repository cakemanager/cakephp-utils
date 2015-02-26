<?php
namespace Utils\Test\TestCase\Model\Behavior;

use CakeManager\Model\Behavior\MetasBehavior;
use Cake\TestSuite\TestCase;

/**
 * CakeManager\Model\Behavior\MetasBehavior Test Case
 */
class MetasBehaviorTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
//        $this->Metas = new MetasBehavior();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Metas);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
