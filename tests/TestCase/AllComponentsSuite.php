<?php

namespace Utils\Test\TestCase;

use Cake\TestSuite\TestSuite;

class AllComponentsSuite extends TestSuite
{

    public static function suite() {
        $suite = new self('Component related tests');
        $suite->addTestDirectory(__DIR__ . DS . 'Controller' . DS . 'Component');
        return $suite;
    }

}
