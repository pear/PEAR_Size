#!@PHP-BIN@
<?php
/**
 * PEAR_Size determines how much filespace is consumed by each PEAR package.
 * Information can be displayed on a channel-by-channel basis and can be
 * drilled down to the role/filetype of each installed file.
 *
 * PHP Version 5
 *
 * @category PEAR
 * @package  PEAR_Size
 * @author   Ken Guest <ken@linux.ie>
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  CVS: <cvs_id>
 * @link     pearsize.php
 *
 * $ pearsize --all
 * $ pearsize Validate_IE
 * $ pearsize Validate_IE --type=test,doc
*/
/**
 * Neded to parse options on command line
 */
require 'Console/Getopt.php';
if (is_file(dirname(__FILE__) . "/../Size/Size.php") === true) {
    include dirname(__FILE__) . "/../Size/Size.php";
} else {
    include 'PEAR/Size.php';
}

/**
 * exit status: OK
 */
define('PEAR_SIZE_OK', 0);
/**
 * exit status: not using the CLI version of php
 */
define('PEAR_SIZE_NON_CLI', 10);
/**
 * exit status: arguments/parameters missing
 */
define('PEAR_SIZE_MISSING_ARGS', 11);
/**
 * exit status: invalid options specified
 */
define('PEAR_SIZE_INVALID_OPTIONS', 12);
/**
 * name of the application/script
 */
define('APP_NAME', basename($_SERVER['PHP_SELF']));
define('APP_VERSION', "@PACKAGE_VERSION@");
/**
 * usage
 *
 * Display either given error message, which can be in an array, or the usage screen.
 *
 * @param mixed $error string or array of strings of error messages to display. If
 *                     null, which is the default, the usage screen is displayed.
 *
 * @access public
 * @return void
 */
function usage($error = null)
{
    $app = APP_NAME;
    if (PEAR::isError($error)) {
        fputs(STDERR, $error->getMessage() . "\n");
    } elseif ($error !== null) {
        if (is_array($error)) {
            foreach ($error as $message) {
                fputs(STDERR, "$message\n");
            }
        } else {
            fputs(STDERR, "$error\n");
        }
    } elseif ($error === null) {
        fputs(STDOUT, "Usage: {$app} [OPTIONS] [PACKAGE]\n");
        fputs(STDOUT, "Display information on how much space an installed ");
        fputs(STDOUT, "PEAR package required.\n\n");
        fputs(STDOUT, "  -a, --all            ");
        fputs(STDOUT, "display information for all installed packages\n");
        fputs(STDOUT, "  -A, --allchannels    ");
        fputs(STDOUT, "list packages from all channels, not just the default one\n");
        fputs(STDOUT, "  -c, --channel        specify which channel\n");
        fputs(STDOUT, "  -h, --human-readable print sizes in human readable ");
        fputs(STDOUT, "format (for example: 492 B 1KB 7MB)\n");
        fputs(STDOUT, "  -H, --si             ");
        fputs(STDOUT, "likewise, but use powers of 1000 not 1024\n");
        fputs(STDOUT, "  -t, --type           ");
        fputs(STDOUT, "specify what type of files are required for the report\n");
        fputs(STDOUT, "                       by default all types are assumed\n");
        fputs(STDOUT, "  -S                   sort by file size\n");
        fputs(STDOUT, "  -v, --verbose        display more detailed information\n");
        fputs(STDOUT, "      --help           display this help and exit\n");
        fputs(STDOUT, "  -V, --version        ");
        fputs(STDOUT, "output version information and exit\n");

        fputs(STDOUT, "\nTypes:\n");
        fputs(STDOUT, "You can specify a subset of roles/file-types to be ");
        fputs(STDOUT, "listed in the report.\nThese roles are those as supported ");
        fputs(STDOUT, "by the PEAR installer.\n");
        fputs(STDOUT, "These are: data, doc, php, script, test\n");

        fputs(STDOUT, "\nExamples:
              $ {$app} --all
              $ {$app} Validate_IE
              $ {$app} -ttest,doc Validate_IE
              $ {$app} --type=test,doc,php -h Validate_IE Date_Holidays
              \n");
    }
}
/**
 * printVersion
 *
 * @access public
 * @return void
 */
function printVersion()
{
    echo  APP_VERSION, "\n";
}
if (php_sapi_name() !== 'cli') {
    echo  "cli version of php required.\n";
    exit(NON_CLI);
}

$pearsize = new PEAR_Size();
$pearsize->setOutputDriver('text');
$channels = array();

$argv         = Console_Getopt::readPHPArgv();
$long_options = array(
        'all',
        'allchannels',
        'channel==',
        'help',
        'human-readable',
        'si',
        'type==',
        'verbose',
        'version',
        );

$options = Console_Getopt::getopt($argv, "aAc:hHt:SVv", $long_options);

if (PEAR::isError($options)) {
    usage($options);
    exit(PEAR_SIZE_INVALID_OPTIONS);
}
if (empty($options[0]) && empty($options[1])) {
    usage();
    exit(PEAR_SIZE_INVALID_OPTIONS);
}

ini_set('max_execution_time', 0);
foreach ($options[0] as $opt) {
    $argument = str_replace('-', '', $opt[0]);
    switch ($argument) {
    case 'version':
    case 'V':
        printVersion();
        exit(0);
    case 'help':
        usage();
        exit(0);
    }
}
$ret = $pearsize->parseCLIOptions($options);
if (PEAR::isError($ret)) {
    usage($ret);
    exit(1);
}
$pearsize->analyse();
$errors = $pearsize->errors;
if (sizeof($errors) > 0) {
    usage($errors);
    exit(1);
}
$pearsize->generateReport();
?>
