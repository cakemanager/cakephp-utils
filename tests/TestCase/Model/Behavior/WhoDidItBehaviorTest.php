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

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * CakeManager\Model\Behavior\WhoDidItBehavior Test Case
 *
 */
class WhoDidItBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.utils.articles',
        'plugin.utils.users'
    ];

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
     * Test if the relations are added to 'createdBy' and 'modifiedBy'
     *
     */
    public function testFind()
    {
        $data = $this->Model->get(1);

        $this->assertEquals(1, $data->createdBy->id);
        $this->assertEquals(1, $data->modifiedBy->id);

        $this->assertEquals(8, count($data->createdBy->toArray()));
        $this->assertEquals(8, count($data->modifiedBy->toArray()));
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

        $this->assertEquals(2, count($data->createdBy->toArray()));
        $this->assertEquals(2, count($data->modifiedBy->toArray()));

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

        $this->assertEquals(8, count($data->createdBy->toArray()));
        $this->assertNull($data->modifiedBy);

        $behavior->config('modified_by', 'modified_by');
    }

    /**
     * Test if the users id's are added when adding an article
     *
     */
    public function testAddArticle()
    {
        $this->assertEquals(3, $this->Model->find('all')->Count());

        $_SESSION['Auth'] = [
            'User' => [
                'id' => 1,
                'username' => 'testing account',
            ]
        ];

        Configure::write('GlobalAuth', $_SESSION['Auth']['User']);

        $_data = [
            'user_id' => 1,
            'title' => 'Fourth Article',
            'body' => 'Fourth Article Body',
            'published' => 'Y',
        ];

        $entity = $this->Model->newEntity($_data);

        $this->Model->save($entity);

        $this->assertEquals(4, $this->Model->find('all')->Count());

        $data = $this->Model->get(4);

        $this->assertEquals("Fourth Article", $data->title);
        $this->assertEquals(1, $data->createdBy->id);
        $this->assertEquals(1, $data->modifiedBy->id);
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
                'id' => 2,
                'username' => 'testing account',
            ]
        ];

        Configure::write('GlobalAuth', $_SESSION['Auth']['User']);

        $data = $this->Model->get(3);

        $_data = $data->toArray();
        $_data['title'] = "Thirth Article Edited";

        $entity = $this->Model->patchEntity($data, $_data);

        $this->Model->save($entity);

        $result = $this->Model->get(3);

        $this->assertEquals("Thirth Article Edited", $result->title);
        $this->assertEquals(1, $result->createdBy->id);
        $this->assertEquals(2, $result->modifiedBy->id);
    }

    /**
     * Test if the relations have different propertynames
     */
    public function testDifferentPropertyNames()
    {
        $this->Model->removeBehavior('WhoDidIt');
        $this->Model->addBehavior('Utils.WhoDidIt', [
            'createdByPropertyName' => 'createdBy_prop',
            'modifiedByPropertyName' => 'modifiedBy_prop'
        ]);

        $data = $this->Model->get(1);

        $this->assertNotNull($data->createdBy_prop);
        $this->assertNull($data->createdBy);
        $this->assertNotNull($data->modifiedBy_prop);
        $this->assertNull($data->modifiedBy);
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
