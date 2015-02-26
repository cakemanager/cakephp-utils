<?php

namespace Utils\Test\TestCase\Model\Behavior;

use Utils\Model\Behavior\WhoDidItBehavior;
use Cake\TestSuite\TestCase;
use Cake\Core\Configure;

/**
 * CakeManager\Model\Behavior\WhoDidItBehavior Test Case
 */
class WhoDidItBehaviorTest extends TestCase
{

    public $fixtures = ['plugin.cake_manager.articles', 'plugin.cake_manager.users'];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Model = \Cake\ORM\TableRegistry::get('Articles');
        $this->Model->addBehavior('Utils.WhoDidIt');

    }

    /**
     * Test if the relations are added to 'created_by' and 'modified_by'
     *
     */
    public function testFind()
    {

        $data = $this->Model->get(1);

        $this->AssertEquals(1, $data->created_by->id);
        $this->AssertEquals(1, $data->modified_by->id);

        $this->AssertEquals(7, count($data->created_by->toArray()));
        $this->AssertEquals(7, count($data->modified_by->toArray()));
    }

    /**
     * Test if the field-option works
     *
     */
    public function testFindWithFields()
    {
        $behavior = $this->Model->behaviors()->get('WhoDidIt');

        $behavior->config('fields', ['id', 'email']);

        $data = $this->Model->get(1);

        $this->AssertEquals(2, count($data->created_by->toArray()));
        $this->AssertEquals(2, count($data->modified_by->toArray()));

        $behavior->config('fields', null);
        $behavior->config('fields', []);
    }

    /**
     * Test if disabeling any option will work
     *
     */
    public function testFindCreatedByOnly()
    {
        $behavior = $this->Model->behaviors()->get('WhoDidIt');

        $behavior->config('modified_by', false);

        $data = $this->Model->get(1);

        $this->AssertEquals(7, count($data->created_by->toArray()));
        $this->AssertEquals(1, $data->modified_by);

        $behavior->config('modified_by', 'modified_by');

    }

    /**
     * Test if the users id's are added when adding an article
     *
     */
    public function testAddArticle()
    {
        $this->AssertEquals(3, $this->Model->find('all')->Count());

        $_SESSION['Auth'] = [
            'User' => [
                'id'       => 1,
                'username' => 'testing account',
            ]
        ];

        $_data = [
            'user_id'   => 1,
            'title'     => 'Fourth Article',
            'body'      => 'Fourth Article Body',
            'published' => 'Y',
        ];

        $entity = $this->Model->newEntity($_data);

        $this->Model->save($entity);

        $this->AssertEquals(4, $this->Model->find('all')->Count());

        $data = $this->Model->get(4);

        $this->AssertEquals("Fourth Article", $data->title);
        $this->AssertEquals(1, $data->created_by->id);
        $this->AssertEquals(1, $data->modified_by->id);
    }

    /**
     * Test if the users id is added when adding an article
     *
     */
    public function testEditArticle()
    {
        // change the users id
        $_SESSION['Auth'] = [
            'User' => [
                'id'       => 2,
                'username' => 'testing account',
            ]
        ];

        $data = $this->Model->get(3);

        $_data = $data->toArray();
        $_data['title'] = "Thirth Article Edited";

        $entity = $this->Model->patchEntity($data, $_data);

        $this->Model->save($entity);

        $result = $this->Model->get(3);

        $this->AssertEquals("Thirth Article Edited", $result->title);
        $this->AssertEquals(1, $result->created_by->id);
        $this->AssertEquals(2, $result->modified_by->id);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->Model = null;

        unset($this->Model);

        parent::tearDown();
    }

}
