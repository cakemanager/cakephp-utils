<?php

namespace Utils\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Short description for class.
 *
 */
class ArticlesFixture extends TestFixture
{

    /**
     * fields property
     *
     * @var array
     */
    public $fields = array(
        'id'           => ['type' => 'integer'],
        'user_id'      => ['type' => 'integer', 'null' => true],
        'title'        => ['type' => 'string', 'null' => true],
        'body'         => 'text',
        'file_path'    => 'text',
        'file_size'    => 'text',
        'file_type'    => 'text',
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
    );

}
