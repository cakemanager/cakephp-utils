<?php

namespace Utils\Test\TestCase\Controller\Component;

use Utils\Controller\Component\AuthorizerComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Utility\Hash;

/**
 * CakeManager\Controller\Component\AuthorizerComponent Test Case
 */
class AuthorizerComponentTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Authorizer = $this->setUpRequest([
            'prefix'     => null,
            'plugin'     => 'utils',
            'controller' => 'users',
            'action'     => 'index'
        ]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->Authorizer->clearData();

        unset($this->Authorizer);
        unset($this->controller);

        parent::tearDown();
    }

    /**
     * Test the setCurrentParams method.
     *
     * This method will set the params (plugin, controller, action) by the controller.
     *
     */
    public function testSetCurrentParams()
    {

        $set = $this->Authorizer->setCurrentParams();

        $this->assertEquals("utils", $set['plugin']);
        $this->assertEquals("users", $set['controller']);
        $this->assertEquals("index", $set['action']);
    }

    /**
     * Test the setController method.
     *
     * This method sets the controller inside the component.
     *
     */
    public function testSetController()
    {
        $set = $this->Authorizer->setCurrentParams();

        $this->assertEquals("utils", $set['plugin']);
        $this->assertEquals("users", $set['controller']);
        $this->assertEquals("index", $set['action']);

        // Setup our component and fake test controller
        $request = new Request(['params' => [
                'plugin'     => 'utils',
                'controller' => 'bookmarks',
                'action'     => 'view'
        ]]);
        $response = new Response();

        $controller = $this->getMock(
                'Cake\Controller\Controller', ['redirect'], [$request, $response]
        );

        $this->Authorizer->setController($controller);

        $set = $this->Authorizer->setCurrentParams();

        $this->assertEquals("utils", $set['plugin']);
        $this->assertEquals("bookmarks", $set['controller']);
        $this->assertEquals("view", $set['action']);
    }

    /**
     * Test the action method.
     *
     * This method is the beginning of the authorization
     *
     */
    public function testAction()
    {
        $this->assertEmpty($this->Authorizer->getData());

        $this->Authorizer->action('index', function($auth) {

        });

        $this->assertNotEmpty($this->Authorizer->getData());
    }

    /**
     * Test the allowRole method.
     *
     */
    public function testAllowRole()
    {
        $this->assertEmpty($this->Authorizer->getData());

        $this->Authorizer->action('index', function($auth) {
            $auth->allowRole(1);
        });

        $this->assertNotEmpty($this->Authorizer->getData());

        $data = $this->Authorizer->getData();

        $this->assertTrue(Hash::get($data, "users.index.roles.1"));
        $this->assertArrayHasKey(1, Hash::get($data, "users.index.roles"));
        $this->assertArrayNotHasKey(2, Hash::get($data, "users.index.roles"));
        $this->assertArrayNotHasKey(3, Hash::get($data, "users.index.roles"));
    }

    /**
     * Test the denyRole method.
     *
     */
    public function testDenyRole()
    {
        $this->assertEmpty($this->Authorizer->getData());

        $this->Authorizer->action('index', function($auth) {
            $auth->denyRole(2);
        });

        $this->assertNotEmpty($this->Authorizer->getData());

        $data = $this->Authorizer->getData();

        $this->assertFalse(Hash::get($data, "users.index.roles.2"));
        $this->assertArrayHasKey(2, Hash::get($data, "users.index.roles"));
        $this->assertArrayNotHasKey(1, Hash::get($data, "users.index.roles"));
        $this->assertArrayNotHasKey(3, Hash::get($data, "users.index.roles"));
    }

    /**
     * Test the setRole method.
     *
     */
    public function testSetRole()
    {
        $this->assertEmpty($this->Authorizer->getData());

        $this->Authorizer->action('index', function($auth) {
            $auth->setRole(1, true);
            $auth->setRole(2, false);
            $auth->setRole(3, false);
            $auth->setRole(4, true);
        });

        $this->assertNotEmpty($this->Authorizer->getData());

        $data = $this->Authorizer->getData();

        $this->assertTrue(Hash::get($data, "users.index.roles.1"));
        $this->assertFalse(Hash::get($data, "users.index.roles.2"));
        $this->assertFalse(Hash::get($data, "users.index.roles.3"));
        $this->assertTrue(Hash::get($data, "users.index.roles.4"));
    }

    /**
     * Test the final Authorize method.
     *
     * This method returns a boolean if the user is allowed to the page
     *
     */
    public function testAuthorize()
    {
        $this->Authorizer->Controller->Auth->setUser([
            'id'      => 1,
            'role_id' => 1,
        ]);

        $this->Authorizer->action('index', function($auth) {
            $auth->allowRole(1);
        });

        $this->assertTrue($this->Authorizer->authorize());

        $this->Authorizer->action('index', function($auth) {
            $auth->denyRole(1);
        });

        $this->assertFalse($this->Authorizer->authorize());

    }

    /**
     * Helper to return a component with a given url
     *
     * @param type $url
     */
    public function setUpRequest($params)
    {

        // Setup our component and fake test controller
        $request = new Request(['params' => $params]);
        $response = new Response();

        $this->controller = $this->getMock(
                'Cake\Controller\Controller', ['redirect'], [$request, $response]
        );

        $this->controller->loadComponent('Auth');

        $this->controller->Auth->setUser([
            'id'       => 1,
            'role_id'  => 1,
            'username' => 'cake',
        ]);

        $registry = new ComponentRegistry($this->controller);
        $authorizer = new AuthorizerComponent($registry);

        $authorizer->setController($this->controller);

        return $authorizer;
    }

}
