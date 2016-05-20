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

use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;

/**
 * CakeManager\Model\Behavior\UploadableBehavior Test Case
 *
 */
class UploadableBehaviorTest extends TestCase
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

        $connection = ConnectionManager::get('test');

        $this->Articles = $this->getMock('Cake\ORM\Table', ['_mkdir', '_moveUploadedFile'], [
            ['table' => 'articles', 'connection' => $connection]
        ]);

        $this->_uploadPath = ROOT . 'webroot' . DS . 'uploads';
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Articles);

        /*
         * if created uploads by _mkdir, delete tree
         */
        if (is_dir($this->_uploadPath)) {
            self::delTree($this->_uploadPath);
        }

        parent::tearDown();
    }

    /**
     * delTree method
     *
     * @param string $dir of directory
     * @return bool
     */
    protected static function _delTree($dir)
    {
        $files = array_diff(scandir($dir), [ '.', '..' ]);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    /**
     * testGetFieldList
     *
     * @return void
     */
    public function testGetFieldList()
    {
        // adding 3 different fields
        $this->Articles->addBehavior('Utils.Uploadable', [
            'fieldWithoutSettings',
            'fieldWithCustomSettings1' => [
                'fields' => [
                    'url' => 'customUrl',
                    'directory' => 'customDirectory',
                    'type' => 'customType',
                    'size' => 'customSize',
                ],
                'removeFileOnUpdate' => true,
                'removeFileOnDelete' => false,
            ],
            'fieldWithCustomSettings2' => [
                'field' => 'user_id',
                'path' => '{ROOT}{WEBROOT}{DS}uploads{DS}{model}{DS}',
                'fileName' => '{field}.{extension}',
            ],
        ]);

        $behavior = $this->Articles->behaviors()->get('Uploadable');

        $action = $behavior->getFieldList();

        // testing field 1
        $this->assertEquals("fieldWithoutSettings", $action['fieldWithoutSettings']['fields']['filePath']);
        $this->assertFalse($action['fieldWithoutSettings']['fields']['type']);
        $this->assertFalse($action['fieldWithoutSettings']['fields']['size']);
        $this->assertTrue($action['fieldWithoutSettings']['removeFileOnUpdate']);
        $this->assertTrue($action['fieldWithoutSettings']['removeFileOnDelete']);
        $this->assertEquals("id", $action['fieldWithoutSettings']['field']);
        $this->assertEquals("{ROOT}{WEBROOT}{DS}uploads{DS}{model}{DS}{field}{DS}", $action['fieldWithoutSettings']['path']);
        $this->assertEquals("{ORIGINAL}", $action['fieldWithoutSettings']['fileName']);

        // testing field 2
        $this->assertEquals("customUrl", $action['fieldWithCustomSettings1']['fields']['url']);
        $this->assertEquals("customDirectory", $action['fieldWithCustomSettings1']['fields']['directory']);
        $this->assertEquals("customType", $action['fieldWithCustomSettings1']['fields']['type']);
        $this->assertEquals("customSize", $action['fieldWithCustomSettings1']['fields']['size']);
        $this->assertTrue($action['fieldWithCustomSettings1']['removeFileOnUpdate']);
        $this->assertFalse($action['fieldWithCustomSettings1']['removeFileOnDelete']);

        // testing field 3
        $this->assertEquals("user_id", $action['fieldWithCustomSettings2']['field']);
        $this->assertEquals("{ROOT}{WEBROOT}{DS}uploads{DS}{model}{DS}", $action['fieldWithCustomSettings2']['path']);
        $this->assertEquals("{field}.{extension}", $action['fieldWithCustomSettings2']['fileName']);
    }

    /**
     * testSaveWithoutFile
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

    /**
     * testSaveWithFile
     *
     * @return void
     */
    public function testSaveWithFile()
    {
        $connection = ConnectionManager::get('test');

        $table = $this->getMock('Cake\ORM\Table', ['_nonExistingMethodElseTheMockWillMockAllMethods'], [
            ['table' => 'articles', 'connection' => $connection]
        ]);

        $table->alias("Articles");

        $behaviorOptions = [
            'file' => [
                'fields' => [
                    'url' => 'url',
                    'directory' => 'directory',
                    'fileName' => 'file_name',
                    'filePath' => 'file_path',
                    'type' => 'type',
                    'size' => 'size',
                ],
                'fileName' => '{primaryKey}_article_{userId}.{extension}',
                'entityReplacements' => [
                    '{primaryKey}' => 'id',
                    '{userId}' => 'user_id'
                ],
            ]
        ];

        $mocks = [ '_moveUploadedFile' ];

        $behaviorMock = $this->getMock('\Utils\Model\Behavior\UploadableBehavior', $mocks, [$table, $behaviorOptions]);

        $behaviorMock->expects($this->any())
            ->method('_moveUploadedFile')
            ->will($this->returnValue(true));

        $table->behaviors()->set('Uploadable', $behaviorMock);

        $data = [
            'id' => 3,
            'user_id' => 5,
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

        $this->assertContains('/uploads/articles/3/3_article_5.png', $get->get('url'));
        $this->assertContains('uploads' . DS . 'articles' . DS . '3' . DS, $get->get('directory'));
        $this->assertEquals("image/png", $get->get('type'));
        $this->assertEquals(11501, $get->get('size'));
        $this->assertContains('3_article_5.png', $get->get('file_name'));
        $this->assertContains('uploads' . DS . 'articles' . DS . '3' . DS . '3_article_5.png', $get->get('file_path'));
    }

    /**
     * testRemoveFileOnUpdate
     *
     * @return void
     */
    public function testRemoveFileOnUpdate()
    {
        $connection = ConnectionManager::get('test');

        $table = $this->getMock('Cake\ORM\Table', ['_nonExistingMethodElseTheMockWillMockAllMethods'], [
            ['table' => 'articles', 'connection' => $connection]
        ]);

        $table->alias("Articles");

        $behaviorOptions = [
            'file' => [
                'fields' => [
                    'url' => 'url',
                    'directory' => 'directory',
                    'fileName' => 'file_name',
                    'filePath' => 'file_path',
                    'type' => 'type',
                    'size' => 'size',
                ],
                'removeFileOnUpdate' => true
            ]
        ];

        $mocks = [ '_moveUploadedFile' ];

        $behaviorMock = $this->getMock('\Utils\Model\Behavior\UploadableBehavior', $mocks, [$table, $behaviorOptions]);

        $behaviorMock->expects($this->any())
            ->method('_moveUploadedFile')
            ->will($this->returnValue(true));

        $table->behaviors()->set('Uploadable', $behaviorMock);

        $tmpName = ROOT . 'README.md';
        $data = [
            'id' => 3,
            'user_id' => 5,
            'title' => 'My first article',
            'body' => 'Content',
            'file' => [
                'name' => 'README.md',
                'type' => 'text/x-markdown',
                'tmp_name' => $tmpName,
                'error' => 0,
                'size' => 11501
            ]
        ];

        $entity = $table->newEntity($data);
        $save = $table->save($entity);

        $get = $table->get(3);

        copy($tmpName, $this->_uploadPath . DS . 'articles' . DS . '3' . DS . 'README.md');

        $this->assertTrue(file_exists($this->_uploadPath . DS . 'articles' . DS . '3' . DS . 'README.md'));
        $this->assertContains('/uploads/articles/3/README.md', $get->get('url'));
        $this->assertContains('uploads' . DS . 'articles' . DS . '3' . DS, $get->get('directory'));
        $this->assertContains('README.md', $get->get('file_name'));
        $this->assertContains('uploads' . DS . 'articles' . DS . '3' . DS . 'README.md', $get->get('file_path'));

        // $behaviorMock->expects($this->any())
        //     ->method('_moveUploadedFile')
        //     ->will($this->returnValue(false));

        // $table->behaviors()->set('Uploadable', $behaviorMock);

        $data = [
            'id' => 3,
            'user_id' => 5,
            'title' => 'My first article',
            'body' => 'Content',
            'file' => [
                'name' => 'README2.md',
                'type' => 'text/x-markdown',
                'tmp_name' => $tmpName,
                'error' => 0,
                'size' => 11501
            ]
        ];
        $entity = $table->newEntity($data);
        $save = $table->save($entity);

        $get = $table->get(3);
        $this->assertFalse(file_exists('uploads' . DS . 'articles' . DS . '3' . DS . 'README.md'));
        $this->assertContains('/uploads/articles/3/README2.md', $get->get('url'));
        $this->assertContains('uploads' . DS . 'articles' . DS . '3' . DS, $get->get('directory'));
        $this->assertContains('README2.md', $get->get('file_name'));
        $this->assertContains('uploads' . DS . 'articles' . DS . '3' . DS . 'README2.md', $get->get('file_path'));
    }

    /**
     * testRemoveFileOnDelete
     *
     * @return void
     */
    public function testRemoveFileOnDelete()
    {
        $connection = ConnectionManager::get('test');

        $table = $this->getMock('Cake\ORM\Table', ['_nonExistingMethodElseTheMockWillMockAllMethods'], [
            ['table' => 'articles', 'connection' => $connection]
        ]);

        $table->alias("Articles");

        $behaviorOptions = [
            'file' => [
                'fields' => [
                    'url' => 'url',
                    'directory' => 'directory',
                    'fileName' => 'file_name',
                    'filePath' => 'file_path',
                    'type' => 'type',
                    'size' => 'size',
                ],
            ]
        ];

        $mocks = [ '_moveUploadedFile' ];

        $behaviorMock = $this->getMock('\Utils\Model\Behavior\UploadableBehavior', $mocks, [$table, $behaviorOptions]);

        $behaviorMock->expects($this->any())
            ->method('_moveUploadedFile')
            ->will($this->returnValue(true));

        $table->behaviors()->set('Uploadable', $behaviorMock);

        $tmpName = ROOT . 'README.md';
        $data = [
            'id' => 3,
            'user_id' => 5,
            'title' => 'My first article',
            'body' => 'Content',
            'file' => [
                'name' => 'README.md',
                'type' => 'text/x-markdown',
                'tmp_name' => $tmpName,
                'error' => 0,
                'size' => 11501
            ]
        ];

        $entity = $table->newEntity($data);
        $save = $table->save($entity);

        $get = $table->get(3);

        copy($tmpName, $this->_uploadPath . DS . 'articles' . DS . '3' . DS . 'README.md');

        $this->assertTrue(file_exists($this->_uploadPath . DS . 'articles' . DS . '3' . DS . 'README.md'));
        $this->assertContains('/uploads/articles/3/README.md', $get->get('url'));
        $this->assertContains('uploads' . DS . 'articles' . DS . '3' . DS, $get->get('directory'));
        $this->assertContains('README.md', $get->get('file_name'));
        // $this->assertContains('uploads' . DS . 'articles' . DS . '3' . DS . 'README.md', $get->get('file_path'));

        $table->delete($get);
        $this->assertFalse(file_exists($this->_uploadPath . DS . 'articles' . DS . '3' . DS . 'README.md'));
    }

    /**
     * testSaveWithWrongType
     *
     * @return void
     */
    public function testSaveWithWrongType()
    {
        $connection = ConnectionManager::get('test');

        $table = $this->getMock('Cake\ORM\Table', ['_nonExistingMethodElseTheMockWillMockAllMethods'], [
            ['table' => 'articles', 'connection' => $connection]
        ]);

        $table->alias("Articles");

        $behaviorOptions = [
            'file' => [
                'fields' => [
                    'url' => 'url',
                    'directory' => 'directory',
                    'fileName' => 'file_name',
                    'filePath' => 'file_path',
                    'type' => 'type',
                    'size' => 'size',
                ],
                'accept_type' => 'image'
            ]
        ];

        $behaviorMock = $this->getMock('\Utils\Model\Behavior\UploadableBehavior', null, [$table, $behaviorOptions]);

        $table->behaviors()->set('Uploadable', $behaviorMock);

        $data = [
            'id' => 3,
            'user_id' => 3,
            'title' => 'My first article',
            'body' => 'Content',
            'file' => [
                'name' => 'README.md',
                'type' => 'text/x-markdown',
                'tmp_name' => ROOT . 'README.md',
                'error' => 0,
                'size' => 11501
            ]
        ];

        $entity = $table->newEntity($data);
        $save = $table->save($entity);

        $get = $table->get(3);

        $this->assertEmpty($get->get('url'));
        $this->assertEmpty($get->get('type'));
        $this->assertEmpty($get->get('size'));
        $this->assertEmpty($get->get('file_name'));
        $this->assertEmpty($get->get('file_path'));
        $this->assertFalse(file_exists($this->_uploadPath . DS . 'articles' . DS . '3' . DS . 'README.md'));
    }
}
