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

use Cake\TestSuite\TestCase;
use Cake\Datasource\ConnectionManager;

/**
 * CakeManager\Model\Behavior\UploadableBehavior Test Case
 * 
 */
class UploadableBehaviorTest extends TestCase
{
    public $fixtures = ['plugin.utils.articles'];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $connection = ConnectionManager::get('test');

        $this->Articles = $this->getMock('Cake\ORM\Table', [
            '_mkdir',
            '_moveUploadedFile'
                ], [
            ['table' => 'articles', 'connection' => $connection]
        ]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Articles);

        parent::tearDown();
    }

    /**
     * Test the method getFieldList
     *
     * Will return the normalized list of fields who will be uploadable
     *
     */
    public function testGetFieldList()
    {

        // adding 3 different fields
        $this->Articles->addBehavior('Utils.Uploadable', [
            'fieldWithoutSettings',
            'fieldWithCustomSettings1' => [
                'fields' => [
                    'directory' => 'customDirectory',
                    'type' => 'customType',
                    'size' => 'customSize',
                ],
                'removeFileOnUpdate' => true,
                'removeFileOnDelete' => false,
            ],
            'fieldWithCustomSettings2' => [
                'field' => 'user_id',
                'path' => '{ROOT}{DS}{WEBROOT}{DS}uploads{DS}{model}{DS}',
                'fileName' => '{field}.{extension}',
            ],
        ]);

        $behavior = $this->Articles->behaviors()->get('Uploadable');

        $action = $behavior->getFieldList();

        // testing field 1
        $this->assertEquals("fieldWithoutSettings", $action['fieldWithoutSettings']['fields']['directory']);
        $this->assertFalse($action['fieldWithoutSettings']['fields']['type']);
        $this->assertFalse($action['fieldWithoutSettings']['fields']['size']);
        $this->assertFalse($action['fieldWithoutSettings']['removeFileOnUpdate']);
        $this->assertTrue($action['fieldWithoutSettings']['removeFileOnDelete']);
        $this->assertEquals("id", $action['fieldWithoutSettings']['field']);
        $this->assertEquals("{ROOT}{DS}{WEBROOT}{DS}uploads{DS}{model}{DS}{field}{DS}", $action['fieldWithoutSettings']['path']);
        $this->assertEquals("{ORIGINAL}", $action['fieldWithoutSettings']['fileName']);

        // testing field 2
        $this->assertEquals("customDirectory", $action['fieldWithCustomSettings1']['fields']['directory']);
        $this->assertEquals("customType", $action['fieldWithCustomSettings1']['fields']['type']);
        $this->assertEquals("customSize", $action['fieldWithCustomSettings1']['fields']['size']);
        $this->assertTrue($action['fieldWithCustomSettings1']['removeFileOnUpdate']);
        $this->assertFalse($action['fieldWithCustomSettings1']['removeFileOnDelete']);

        // testing field 3
        $this->assertEquals("user_id", $action['fieldWithCustomSettings2']['field']);
        $this->assertEquals("{ROOT}{DS}{WEBROOT}{DS}uploads{DS}{model}{DS}", $action['fieldWithCustomSettings2']['path']);
        $this->assertEquals("{field}.{extension}", $action['fieldWithCustomSettings2']['fileName']);
    }

    /**
     * Test the normalizeAll method.
     *
     * This will do the same as the getFieldList method
     *
     */
    public function testNormalizeAll()
    {

        $options = [
            'fieldWithoutSettings',
            'fieldWithCustomSettings1' => [
                'fields' => [
                    'directory' => 'customDirectory',
                    'type' => 'customType',
                    'size' => 'customSize',
                ],
                'removeFileOnUpdate' => true,
                'removeFileOnDelete' => false,
            ],
            'fieldWithCustomSettings2' => [
                'field' => 'user_id',
                'path' => '{ROOT}{DS}{WEBROOT}{DS}uploads{DS}{model}{DS}',
                'fileName' => '{field}.{extension}',
            ],
        ];

        // adding 3 different fields
        $this->Articles->addBehavior('Utils.Uploadable', $options);

        $behavior = $this->Articles->behaviors()->get('Uploadable');

        $action = $behavior->getFieldList(['normalize' => false]);

        $_options = [
            'fieldWithoutSettings' => [],
            'fieldWithCustomSettings1' => [
                'fields' => [
                    'directory' => 'customDirectory',
                    'type' => 'customType',
                    'size' => 'customSize',
                ],
                'removeFileOnUpdate' => true,
                'removeFileOnDelete' => false,
            ],
            'fieldWithCustomSettings2' => [
                'field' => 'user_id',
                'path' => '{ROOT}{DS}{WEBROOT}{DS}uploads{DS}{model}{DS}',
                'fileName' => '{field}.{extension}',
            ],
        ];

        $this->assertEquals($_options, $action);

        $action = $behavior->normalizeAll();

        $this->assertNotEquals($_options, $action);
    }

    /**
     * Test saving with loading the behavior but without setting up an field to upload
     *
     * @return void
     */
    public function testSaveWithoutFile()
    {

        $this->Articles->addBehavior('Utils.Uploadable');

        $data = [
            'id' => 1,
            'user_id' => 1,
            'title' => 'My first article',
            'body' => 'Content',
        ];

        $entity = $this->Articles->newEntity($data);

        $save = $this->Articles->save($entity);

        $this->assertEquals(1, $save->get('id'));
        $this->assertEquals(1, $save->get('user_id'));
        $this->assertEquals('My first article', $save->get('title'));
        $this->assertEquals('Content', $save->get('body'));

        $this->Articles->removeBehavior('Uploadable');

        $this->Articles->addBehavior('Utils.Uploadable', [
            'file'
        ]);

        $data = [
            'id' => 2,
            'user_id' => 2,
            'title' => 'My second article',
            'body' => 'Content',
        ];

        $entity = $this->Articles->newEntity($data);

        $save = $this->Articles->save($entity);

        $this->assertEquals(2, $save->get('id'));
        $this->assertEquals(2, $save->get('user_id'));
        $this->assertEquals('My second article', $save->get('title'));
        $this->assertEquals('Content', $save->get('body'));
    }

    public function testSaveWithFile()
    {

        $connection = ConnectionManager::get('test');

        $table = $this->getMock('Cake\ORM\Table', [
            '_nonExistingMethodElseTheMockWillMockAllMethods',
                ], [
            ['table' => 'articles', 'connection' => $connection]
        ]);

        $table->alias("Articles");

        $behaviorOptions = [
            'file' => [
                'fields' => [
                    'directory' => 'file_path',
                    'type' => 'file_type',
                    'size' => 'file_size',
                ],
            ]
        ];

        $behaviorMock = $this->getMock(
                '\Utils\Model\Behavior\UploadableBehavior', [ '_mkdir', '_MoveUploadedFile'], [$table, $behaviorOptions]
        );

        $behaviorMock->expects($this->any())
                ->method('_mkdir')
                ->will($this->returnValue(true));
        $behaviorMock->expects($this->any())
                ->method('_MoveUploadedFile')
                ->will($this->returnValue(true));

        $table->behaviors()->set('Uploadable', $behaviorMock);


        $data = [
            'id' => 3,
            'user_id' => 3,
            'title' => 'My first article',
            'body' => 'Content',
            'file' => [
                'name' => 'cakemanager.png',
                'type' => 'image/png',
                'tmp_name' => 'somepath/cakemanager.png',
                'error' => 0,
                'size' => 11501,
            ]
        ];

        $entity = $table->newEntity($data);
        $save = $table->save($entity);

        $get = $table->get(3);

        $this->assertEquals('uploads\articles\3\cakemanager.png', $get->get('file_path'));
        $this->assertEquals("image/png", $get->get('file_type'));
        $this->assertEquals(11501, $get->get('file_size'));
    }
}
