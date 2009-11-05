<?php
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "PEAR_Size_Test_CLI::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'PEAR/Size.php';
require_once 'PEAR/Size/CLI.php';

require_once 'PEAR/Registry.php';
/**
 * A test suite class to test the standard PEAR_Size API/CLI interactions.
 *
 * @category  PEAR
 * @package   PEAR_Size
 * @author    Ken Guest <ken@linux.ie>
 * @copyright 2008 Ken Guest
 * @license   LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version   Release: @PACKAGE_VERSION@
 * @link      http://pear.php.net/package/PEAR_Size
 * @since     Available since PEAR_Size 0.1.5
 */
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
        $reg = new PEAR_Registry;
        if ($reg->getPackage("PEAR_Size") == null) {
            $this->markTestSkipped("PEAR_Size is not installed.");
        }
        $this->cli = new PEAR_Size_CLI();
        $this->genericResponse = "Usage: pearsize [OPTIONS] [PACKAGE]
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
  -0, --killzero       do not output zero values in verbose mode

Types:
You can specify a subset of roles/file-types to be listed in the report.
These roles are those as supported by the PEAR installer.
These are: data, doc, ext, php, script, src, test

Examples:
                $ pearsize --all
                $ pearsize Console_Table
                $ pearsize -ttest,doc Console_Table
                $ pearsize --type=test,doc,php -h Console_Table Date_Holidays\n";
        $this->genericIntegratedResponse = "Usage: pear size [OPTIONS] [PACKAGE]
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
  -0, --killzero       do not output zero values in verbose mode

Types:
You can specify a subset of roles/file-types to be listed in the report.
These roles are those as supported by the PEAR installer.
These are: data, doc, ext, php, script, src, test

Examples:
                $ pear size --all
                $ pear size Console_Table
                $ pear size -ttest,doc Console_Table
                $ pear size --type=test,doc,php -h Console_Table Date_Holidays\n";

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
     * Assert results of pearsize script
     *
     * @param string $args Arguments list that will be pass to the 'pearsize' command
     * @param array  $exp  pearsize output results expected
     *
     * @return void
     */
    private function assertScriptExec($args, $exp, $status = 0)
    {
        $ps      = PATH_SEPARATOR;
        /*
        $command = '@PHP-BIN@ '
                 . '-d include_path=.' . $ps . '@PEAR-DIR@ '
                 . '-f @BIN-DIR@/pearsize -- '; */
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

    /**
     * Assert results of pear size script as integrated with the pear command.
     *
     * @param string $args Arguments list that will be pass to the 'pear size' command
     * @param array  $exp  pear size output results expected
     *
     * @return void
     */

    private function assertPEARHelpExec($args, $exp, $status = 0)
    {
        $ps      = PATH_SEPARATOR;
        $command = '/usr/bin/pear help ';
        $return  = 0;

        ob_start();
        passthru("$command $args 2>&1 ", $return);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($exp, $output);
        $this->assertEquals($status, $return);
    }
    /**
     * Assert results of pear size script as integrated with the pear command.
     *
     * @param string $args Arguments list that will be pass to the 'pear size' command
     * @param array  $exp  pear size output results expected
     *
     * @return void
     */
    private function assertIntegratedExec($args, $exp, $status = 0)
    {
        $ps      = PATH_SEPARATOR;
        $command = '/usr/bin/pear size ';
        $return  = 0;

        ob_start();
        passthru("$command $args", $return);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($exp, $output);
        $this->assertEquals($status, $return);
    }

    /**
     * Test response when channel/package not found.
     *
     * @return void
     */
    public function testNotFound() {
        $expect = 'Package "Imaginary_package" not found' . "\n\n";
        $this->assertScriptExec(' Imaginary_package 2>&1',  $expect, 0);
        $this->assertIntegratedExec(' Imaginary_package 2>&1',  $expect, 0);
    }

    /**
     * Test retrieving PEAR_Size version information.
     *
     * @return void
     */
    public function testVersion() {
        $expect = "@PACKAGE_VERSION@\n";
        $this->assertScriptExec('-V', $expect);
        $this->assertIntegratedExec('-V', $expect);
    }

    /**
     * Test default output.
     *
     * @return void
     */
    public function testDefault() {
        $this->assertScriptExec('', $this->genericResponse, 12);
        $this->assertIntegratedExec('', $this->genericIntegratedResponse, 0);
    }

    /**
     * Test response when CSV output is required but no packages have been
     * specified.
     *
     * @return void
     */
    public function testCSVNoArgs() {
        $this->assertScriptExec('-C', $this->genericResponse, 1);
        $this->assertIntegratedExec('-C', $this->genericIntegratedResponse, 0);
    }

    /**
     * Test response when XML output is required but no packages have been
     * specified.
     *
     * @return void
     */
    public function testXMLNoArgs() {
        $this->assertScriptExec('-X', $this->genericResponse, 1);
        $this->assertIntegratedExec('-X', $this->genericIntegratedResponse, 0);
    }

    /**
     * Test response when help is requested via the pearsize script.
     *
     * @return void
     */
    public function testHelp() {
        $this->assertScriptExec('--help', $this->genericResponse, 0);
    }

    /**
     * Test response when help is requested via the pear command.
     *
     * @return void
     */
    public function testPEARHelp() {
        $expect = "pear size [options] [PACKAGE ...]
Display information on how much space an installed PEAR package requires.
Options:
  -a, --all
        display information for all installed packages
  -A, --allchannels
        list packages from all channels, not just the default one
  -c CHANNEL, --channel=CHANNEL
        specify which channel
  -C, --csv
        output results in CSV format (sizes are measured in bytes).
  -h, --human-readable
        print sizes in human readable format (for example: 492 B 1KB 7MB)
  -H, --si
        likewise, but use powers of 1000 not 1024
  -t TYPES, --type=TYPES
        specify what type of files are required for the report
  -s, --summarise
        display channel summary view
  -S, --fsort
        sort by file size
  -v, --verbose
        display more detailed information
  -V, --version
        output version information and exit
  -X, --xml
        output results in XML format
  -0, --killzero
        do not output zero values in verbose mode

";
        $this->assertPEARHelpExec('size', $expect, 1);
    }
}

if (PHPUnit_MAIN_METHOD == "PEAR_Size_Test_CLI::main") {
    PEAR_Size_Test_CLI::main();
}
?>
