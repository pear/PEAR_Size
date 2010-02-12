<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'PEAR_Size_AllTests::main');
}

require_once 'PHPUnit/TextUI/TestRunner.php';


require_once 'PEAR_Size_TestSuite_Standard.php';
require_once 'PEAR_Size_TestSuite_CLI.php';
require_once 'PEAR_Size_TestSuite_Bugs.php';


class PEAR_Size_AllTests
{
    public static function main()
    {

        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PEAR_Size Tests');
        $suite->addTestSuite('PEAR_Size_Test_Standard');
        $suite->addTestSuite('PEAR_Size_Test_CLI');
        $suite->addTestSuite('PEAR_Size_Test_Bugs');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'PEAR_Size_AllTests::main') {
    PEAR_Size_AllTests::main();
}
?>
