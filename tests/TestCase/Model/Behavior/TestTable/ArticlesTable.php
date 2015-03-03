<?php

namespace Utils\Test\TestCase\Model\Behavior\TestTable;

use Cake\ORM\Table;

class ArticlesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('articles');
        $this->alias('Articles');
    }

//    /**
//     * Move Uploaded File Layer
//     *
//     * @param string $source
//     * @param string $path
//     * @return
//     */
//    public function _move_uploaded_file($source, $path)
//    {
//        return move_uploaded_file($source, $path);
//    }
//
//    /**
//     * MkDir Layer
//     *
//     * @param type $pathname
//     * @param type $mode
//     * @param type $recursive
//     */
//    public function _mkdir($pathname, $mode, $recursive)
//    {
//        return mkdir($pathname, $mode, $recursive);
//    }

}
