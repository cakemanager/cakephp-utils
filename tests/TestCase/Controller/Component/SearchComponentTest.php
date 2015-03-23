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
namespace Utils\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Utils\Controller\Component\SearchComponent;

/**
 * Utils\Controller\Component\SearchComponent Test Case
 */
class SearchComponentTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Articles' => 'plugin.utils.articles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        // Setup our component and fake test controller
        $collection = new ComponentRegistry();
        $this->Search = new SearchComponent($collection);

        $this->Controller = $this->getMock('Cake\Controller\Controller', ['redirect']);
        $this->Search->setController($this->Controller);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Search);
        unset($this->Controller);

        parent::tearDown();
    }

    /**
     * testAddFilter
     *
     * Tests adding multiple filters and configurations.
     *
     * @return void
     */
    public function testAddFilter()
    {
        $this->assertEmpty($this->Search->config('filters'));

        $this->Search->addFilter('TestFilter1');

        $this->assertArrayHasKey('TestFilter1', $this->Search->config('filters'));

        $_settings = [
            'field' => 'TestFilter1',
            'column' => 'TestFilter1',
            'operator' => 'LIKE',
            'attributes' => [
                'label' => false,
                'type' => 'text',
                'placeholder' => null
            ],
            'options' => false
        ];

        $this->assertEquals($_settings, $this->Search->config('filters.TestFilter1'));

        $this->Search->addFilter('TestFilter2', [
            'field' => 'customField',
            'column' => 'customColumn',
            'operator' => '=',
            'attributes' => [
                'placeholder' => 'customPlaceHolder'
            ],
            'options' => [
                'Test1',
                'Test2'
            ]
        ]);

        $_settings = [
            'field' => 'customField',
            'column' => 'customColumn',
            'operator' => '=',
            'attributes' => [
                'placeholder' => 'customPlaceHolder'
            ],
            'options' => [
                'Test1',
                'Test2'
            ]
        ];

        $this->assertEquals($_settings, $this->Search->config('filters.TestFilter2'));
    }

    public function testRemoveFilter()
    {
        $this->Search->addFilter('TestFilter1');
        $this->Search->addFilter('TestFilter2');
        $this->Search->addFilter('TestFilter3');

        $this->assertArrayHasKey('TestFilter1', $this->Search->config('filters'));
        $this->assertArrayHasKey('TestFilter2', $this->Search->config('filters'));
        $this->assertArrayHasKey('TestFilter3', $this->Search->config('filters'));

        $this->Search->removeFilter('TestFilter3');

        $this->assertArrayHasKey('TestFilter1', $this->Search->config('filters'));
        $this->assertArrayHasKey('TestFilter2', $this->Search->config('filters'));
        $this->assertArrayNotHasKey('TestFilter3', $this->Search->config('filters'));

        $this->Search->removeFilter('TestFilter2');

        $this->assertArrayHasKey('TestFilter1', $this->Search->config('filters'));
        $this->assertArrayNotHasKey('TestFilter2', $this->Search->config('filters'));
        $this->assertArrayNotHasKey('TestFilter3', $this->Search->config('filters'));
    }

    /**
     * testBeforeRender
     *
     * Tests if the variable 'searchFilters' will be set.
     *
     * @return void
     */
    public function testBeforeRender()
    {
        $event = new Event('Controller.beforeFilter', $this->Controller);

        $this->assertEmpty($this->Controller->viewVars);

        $this->Search->addFilter('TestFilter1');
        $this->Search->addFilter('TestFilter2');

        $this->Search->beforeRender($event);

        $this->assertArrayHasKey('TestFilter1', $this->Controller->viewVars['searchFilters']);
        $this->assertArrayHasKey('TestFilter2', $this->Controller->viewVars['searchFilters']);
    }

    /**
     * testSearch
     *
     * Tests search wich will return one result.
     *
     * @return void
     */
    public function testSearch()
    {
        $this->Articles = TableRegistry::get('Articles');
        $query = $this->Articles->find('all');

        $this->assertEquals(3, $query->Count());

        $this->Search->addFilter('Title');

        // adding search querys
        $this->Controller->request->query['Title'] = 'First Article';

        $search = $this->Search->search($query);

        $this->assertEquals(1, $search->Count());

        $data = $search->toArray();

        $this->assertEquals('First Article', $data[0]->get('title'));
    }

    /**
     * testSearchMultiple
     *
     * Tests search on 'Article' wich will return multiple results.
     *
     * @return void
     */
    public function testSearchMultiple()
    {
        $this->Articles = TableRegistry::get('Articles');
        $query = $this->Articles->find('all');

        $this->assertEquals(3, $query->Count());

        $this->Search->addFilter('Title');

        // adding search querys
        $this->Controller->request->query['Title'] = 'Article';

        $search = $this->Search->search($query);

        $this->assertEquals(3, $search->Count());

        $data = $search->toArray();

        $this->assertEquals('First Article', $data[0]->get('title'));
        $this->assertEquals('Second Article', $data[1]->get('title'));
        $this->assertEquals('Third Article', $data[2]->get('title'));
    }
}
