<?php
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "PEAR_Size_Test_CLI::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'PEAR/Size.php';
require_once 'PEAR/Size/CLI.php';

class PEAR_Size_Test_CLI extends PHPUnit_Framework_TestCase
{

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        include_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite('PEAR_Size CLI Tests');
        PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->cli = new PEAR_Size_CLI();
    }

    /**
     * Tears down the fixture.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown()
    {
        unset($this->cli);
    }

    /**
     * Assert results of php exec returns
     *
     * @param string $args Arguments list that will be pass to the 'pearsize' command
     * @param array  $exp  pearsize output results expected
     *
     * @return void
     */
    private function assertPhpExec($args, $exp, $status = 0)
    {
        $ps      = PATH_SEPARATOR;
        /*
        $command = '@php_bin@ '
                 . '-d include_path=.' . $ps . '@php_dir@ '
                 . '-f @bin_dir@/pearsize -- '; */
        $command = '/usr/bin/php '
                 . '-d include_path=.' . $ps . '/usr/share/php '
                 . '-f /usr/bin/pearsize -- ';

        $return = 0;

        ob_start();
        passthru("$command $args", $return);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($exp, $output);
        $this->assertEquals($status, $return);
    }

    public function testNotFound() {
        $expect = 'Package "Imaginary_package" not found' . "\n\n";
        $this->assertPhpExec(' Imaginary_package 2>&1',  $expect, 0);
    }

    public function testVersion() {
        $expect = "0.1.4\n";
        $this->assertPhpExec('-V', $expect);
    }

    public function testDefault() {
        $expect = "Usage: pearsize [OPTIONS] [PACKAGE]
Display information on how much space an installed PEAR package required.

  -a, --all            display information for all installed packages
  -A, --allchannels    list packages from all channels, not just the default one
  -c, --channel        specify which channel
  -C, --csv            output results in CSV format (sizes are measured in bytes).
  -h, --human-readable print sizes in human readable format (for example: 492 B 1KB 7MB)
  -H, --si             likewise, but use powers of 1000 not 1024
  -t, --type           specify what type of files are required for the report
                       by default all types are assumed
  -s, --summarise      display channel summary view
  -S, --fsort          sort by file size
  -v, --verbose        display more detailed information
      --help           display this help and exit
  -V, --version        output version information and exit
  -X, --xml            output results in XML format

Types:
You can specify a subset of roles/file-types to be listed in the report.
These roles are those as supported by the PEAR installer.
These are: data, doc, ext, php, script, src, test

Examples:
                $ pearsize --all
                $ pearsize Console_Table
                $ pearsize -ttest,doc Console_Table
                $ pearsize --type=test,doc,php -h Console_Table Date_Holidays\n";
    $this->assertPhpExec('', $expect, 12);
    }

    public function testHelp() {
        $expect = "Usage: pearsize [OPTIONS] [PACKAGE]
Display information on how much space an installed PEAR package required.

  -a, --all            display information for all installed packages
  -A, --allchannels    list packages from all channels, not just the default one
  -c, --channel        specify which channel
  -C, --csv            output results in CSV format (sizes are measured in bytes).
  -h, --human-readable print sizes in human readable format (for example: 492 B 1KB 7MB)
  -H, --si             likewise, but use powers of 1000 not 1024
  -t, --type           specify what type of files are required for the report
                       by default all types are assumed
  -s, --summarise      display channel summary view
  -S, --fsort          sort by file size
  -v, --verbose        display more detailed information
      --help           display this help and exit
  -V, --version        output version information and exit
  -X, --xml            output results in XML format

Types:
You can specify a subset of roles/file-types to be listed in the report.
These roles are those as supported by the PEAR installer.
These are: data, doc, ext, php, script, src, test

Examples:
                $ pearsize --all
                $ pearsize Console_Table
                $ pearsize -ttest,doc Console_Table
                $ pearsize --type=test,doc,php -h Console_Table Date_Holidays\n";
    $this->assertPhpExec('--help', $expect, 0);
    }

}

if (PHPUnit_MAIN_METHOD == "PEAR_Size_Test_CLI::main") {
    PEAR_Size_Test_CLI::main();
}
?>
