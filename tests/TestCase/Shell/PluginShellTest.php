<?php
namespace Utils\Test\TestCase\Shell;

use Cake\TestSuite\TestCase;
use Utils\Shell\PluginShell;

/**
 * Utils\Shell\PluginShell Test Case
 */
class PluginShellTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMock('Cake\Console\ConsoleIo');
        $this->Plugin = new PluginShell($this->io);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Plugin);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
