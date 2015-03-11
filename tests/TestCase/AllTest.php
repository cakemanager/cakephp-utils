<?php
namespace Utils\Test\TestSuite;

use Cake\Core\Plugin;
use Cake\TestSuite\TestSuite;

class AllTest extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new TestSuite('All Utils plugin tests');
        $path = Plugin::path('Utils');
        $testPath = $path . DS . 'tests' . DS . 'TestCase';

        if (!is_dir($testPath)) {
            return $suite;
        }

        $suite->addTestDirectoryRecursive($testPath);

        return $suite;
    }
}