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
    public $fields = [
        'id' => [
            'type' => 'integer'
        ],
        'user_id' => [
            'type' => 'integer',
            'null' => true
        ],
        'title' => [
            'type' => 'string',
            'null' => true
        ],
        'body' => 'text',
        'state' => [
            'type' => 'integer',
            'default' => 1
        ],
        'url' => 'text',
        'directory' => 'text',
        'type' => 'text',
        'size' => 'text',
        'file_name' => 'text',
        'file_path' => 'text',
        'published' => [
            'type' => 'string',
            'length' => 1,
            'default' => 'N'
        ],
        'created_by' => [
            'type' => 'integer'
        ],
        'modified_by' => [
            'type' => 'integer'
        ],
        'created_by_second' => [
            'type' => 'integer'
        ],
        'modified_by_second' => [
            'type' => 'integer'
        ],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
    ];

    /**
     * records property
     *
     * @var array
     */
    public $records = [
        ['user_id' => 1, 'state' => 1, 'title' => 'First Article', 'body' => 'First Article Body', 'published' => 'Y', 'created_by' => 1, 'modified_by' => 1],
        ['user_id' => 3, 'state' => 0, 'title' => 'Second Article', 'body' => 'Second Article Body', 'published' => 'Y', 'created_by' => 1, 'modified_by' => 1],
        ['user_id' => 1, 'state' => -1, 'title' => 'Third Article', 'body' => 'Third Article Body', 'published' => 'Y', 'created_by' => 1, 'modified_by' => 1]
    ];
}
