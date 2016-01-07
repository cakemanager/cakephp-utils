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
namespace Utils\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Utils\View\Helper\SearchHelper;

/**
 * Utils\View\Helper\SearchHelper Test Case
 */
class SearchHelperTest extends TestCase
{

    protected $data;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Search = new SearchHelper($view);

        $this->data = [
            'title' => [
                'field' => 'title',
                'column' => 'title',
                'operator' => 'LIKE',
                'attributes' => [
                    'label' => false,
                    'placeholder' => 'title'
                ],
                'options' => false
            ],
            'category' => [
                'field' => 'category',
                'column' => 'category',
                'operator' => '=',
                'attributes' => [
                    'label' => false,
                    'placeholder' => 'category',
                    'empty' => true
                ],
                'options' => [
                    1 => 'category 1',
                    2 => 'category 2',
                    3 => 'category 3',
                    4 => 'category 4'
                ]
            ]
        ];
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Search);
        unset($this->data);

        parent::tearDown();
    }
    
    public function testFilterForm()
    {
        $result = $this->Search->filterForm($this->data);
        
        $this->assertContains('<form method="get" accept-charset="utf-8" action="/">', $result);
        $this->assertContains('<input type="text" name="title" placeholder="title" id="title"/>', $result);
        $this->assertContains('<select name="category" placeholder="category" id="category">', $result);
        $this->assertContains('<option value=""></option>', $result);
        $this->assertContains('<option value="1">category 1</option>', $result);
        $this->assertContains('<option value="2">category 2</option>', $result);
        $this->assertContains('<option value="3">category 3</option>', $result);
        $this->assertContains('<option value="4">category 4</option>', $result);
        $this->assertContains('<button type="submit">Filter</button></form>', $result);
    }
}
