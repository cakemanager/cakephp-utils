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
 * UsersFixture
 *
 * Note: This Fixture is a clone of the UsersFixture of the CakeManager
 * The clone is made to make the tests work without the CakeManager Plugin
 *
 */
class UsersFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id' => [
            'type' => 'integer',
        ],
        'role_id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => true,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],
        'active' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => true,
            'default' => 0,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],
        'activation_key' => [
            'type' => 'string',
            'length' => 255,
            'unsigned' => false,
            'null' => true,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],
        'email' => [
            'type' => 'string',
            'length' => 255,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'password' => [
            'type' => 'string',
            'length' => 255,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'created' => [
            'type' => 'datetime',
            'length' => null,
            'null' => true,
            'default' => null,
            'comment' => '',
            'precision' => null
        ],
        'modified' => [
            'type' => 'datetime',
            'length' => null,
            'null' => true,
            'default' => null,
            'comment' => '',
            'precision' => null
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB', 'collation' => 'latin1_swedish_ci'
        ],
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'role_id' => 1,
            'email' => 'bob@email.nl',
            'active' => 1,
            'password' => '$2y$10$tg4qyRQVvrfHIXmhsJ2Ks.Fr3/.OCfk9JyXhXLp85AWsur1BcRRhW',
            'created' => '2014-12-23 00:43:20',
            'modified' => '2014-12-23 00:43:20',
        ],
        [
            'id' => 2,
            'role_id' => 1,
            'email' => 'jp@email.nl',
            'active' => 1,
            'password' => '$2y$10$tg4qyRQVvrfHIXmhsJ2Ks.Fr3/.OCfk9JyXhXLp85AWsur1BcRRhW',
            'created' => '2014-12-23 00:43:20',
            'modified' => '2014-12-23 00:43:20'
        ],
        [
            'id' => 3,
            'role_id' => 2,
            'email' => 'jon@email.nl',
            'active' => 1,
            'password' => '$2y$10$tg4qyRQVvrfHIXmhsJ2Ks.Fr3/.OCfk9JyXhXLp85AWsur1BcRRhW',
            'created' => '2014-12-23 00:43:20',
            'modified' => '2014-12-23 00:43:20'
        ],
        [
            'id' => 4,
            'role_id' => 3,
            'email' => 'thomas@email.nl',
            'active' => 1,
            'password' => '$2y$10$tg4qyRQVvrfHIXmhsJ2Ks.Fr3/.OCfk9JyXhXLp85AWsur1BcRRhW',
            'created' => '2014-12-23 00:43:20',
            'modified' => '2014-12-23 00:43:20'
        ],
    ];
}
