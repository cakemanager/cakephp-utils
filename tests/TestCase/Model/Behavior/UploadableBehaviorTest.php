<?php
namespace Utils\Test\TestCase\Model\Behavior;

use Utils\Model\Behavior\UploadableBehavior;
use Cake\TestSuite\TestCase;

/**
 * CakeManager\Model\Behavior\UploadableBehavior Test Case
 */
class UploadableBehaviorTest extends TestCase
{

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
        unset($this->Uploadable);

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
