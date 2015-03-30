<?php namespace Utils\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Utils\View\Helper\SearchHelper;

/**
 * Utils\View\Helper\SearchHelper Test Case
 */
class SearchHelperTest extends TestCase
{

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
                    'type' => 'text',
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
                    'type' => 'text',
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
        $this->assertContains('<input type="text" name="title" placeholder="title" id="title">', $result);
        $this->assertContains('<select name="category" placeholder="category">', $result);
        $this->assertContains('<option value=""></option>', $result);
        $this->assertContains('<option value="1">category 1</option>', $result);
        $this->assertContains('<option value="2">category 2</option>', $result);
        $this->assertContains('<option value="3">category 3</option>', $result);
        $this->assertContains('<option value="4">category 4</option>', $result);
        $this->assertContains('<button type="submit">Filter</button></form>', $result);
    }
}
