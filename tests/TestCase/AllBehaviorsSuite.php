<?php

namespace Utils\Test\TestCase;

use Cake\TestSuite\TestSuite;


class AllBehaviorsSuite extends TestSuite
{

    public static function suite() {
        $suite = new self('Behavior related tests');
        $suite->addTestDirectory(__DIR__ . DS . 'Model' . DS . 'Behavior');
        return $suite;
    }

}
