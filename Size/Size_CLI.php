<?php
/**
 * PEAR_Size
 *
 * PHP Version 5
 *
 * @category PEAR
 * @package  PEAR_Size
 * @author   Ken Guest <ken@linux.ie>
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PEAR_Size
 */

/**
 * This class invokes the PEAR_Size for command line scripts.
 *
 * @category  PEAR
 * @package   PEAR_Size
 * @author    Ken Guest <ken@linux.ie>
 * @copyright 2008 Ken Guest
 * @license   LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version   Release: @PACKAGE_VERSION@
 * @link      http://pear.php.net/package/PEAR_Size
 */
class PEAR_Size_CLI
{
    /**
     * exit status: OK
     */
    const PEAR_SIZE_OK =  0;
    /**
     * exit status: not using the CLI version of php
     */
    const PEAR_SIZE_NON_CLI =  10;
    /**
     * exit status: arguments/parameters missing
     */
    const PEAR_SIZE_MISSING_ARGS =  11;
    /**
     * exit status: invalid options specified
     */
    const PEAR_SIZE_INVALID_OPTIONS =  12;
    /**
     * version of the application/script
     */
    const APP_VERSION = "@PACKAGE_VERSION@";


    /**
     * application name
     */
    var $_appName = null;

    /**
    * return name of app/script using this class.
    *
    * @access public
    * @return string
    */
    public function appName()
    {
        if ($this->_appName === null) {
            $name = basename($_SERVER['PHP_SELF']);
            return $name;
        } else {
            return $this->_appName;
        }
    }

    /**
     * set application name (used by plugins to PEAR etc)
     *
     * @param string $name application name
     *
     * @access public
     * @return void
     */
    public function setAppName($name)
    {
        $this->_appName = $name;
    }
    /**
    * usage
    *
    * Display either given error message, which can be in an array, or
    * the usage screen.
    *
    * @param mixed $error string or array of strings of error messages to display. If
    *                     null, which is the default, the usage screen is displayed.
    *
    * @access public
    * @return void
    */
    public function usage($error = null)
    {
        $app = PEAR_Size_CLI::appName();

        if (PEAR::isError($error)) {
            fputs(STDERR, $error->getMessage() . "\n");
            fputs(STDERR, "\n");
        } elseif (($error !== null) && ($error !== 'usage')) {
            if (is_array($error)) {
                foreach ($error as $message) {
                    fputs(STDERR, "$message\n");
                }
            } else {
                fputs(STDERR, "$error\n");
            }
            fputs(STDERR, "\n");
        } elseif (($error === null ) || ($error == 'usage')) {
            fputs(STDOUT, "Usage: {$app} [OPTIONS] [PACKAGE]\n");
            fputs(STDOUT, "Display information on how much space an installed ");
            fputs(STDOUT, "PEAR package required.\n\n");
            fputs(STDOUT, "  -a, --all            ");
            fputs(STDOUT, "display information for all installed packages\n");
            fputs(STDOUT, "  -A, --allchannels    ");
            fputs(STDOUT, "list packages from all channels, ");
            fputs(STDOUT, "not just the default one\n");
            fputs(STDOUT, "  -c, --channel        specify which channel\n");
            fputs(STDOUT, "  -C, --csv            output results in CSV format ");
            fputs(STDOUT, "(sizes are measured in bytes).\n");
            fputs(STDOUT, "  -h, --human-readable print sizes in human readable ");
            fputs(STDOUT, "format (for example: 492 B 1KB 7MB)\n");
            fputs(STDOUT, "  -H, --si             ");
            fputs(STDOUT, "likewise, but use powers of 1000 not 1024\n");
            fputs(STDOUT, "  -t, --type           ");
            fputs(STDOUT, "specify what type of files are required ");
            fputs(STDOUT, "for the report\n");
            fputs(STDOUT, "                       by default all ");
            fputs(STDOUT, "types are assumed\n");
            fputs(STDOUT, "  -s, --summarise      display channel summary view\n");
            fputs(STDOUT, "  -S, --fsort          sort by file size\n");
            fputs(STDOUT, "  -v, --verbose        ");
            fputs(STDOUT, "display more detailed information\n");
            fputs(STDOUT, "      --help           display this help and exit\n");
            fputs(STDOUT, "  -V, --version        ");
            fputs(STDOUT, "output version information and exit\n");
            fputs(STDOUT, "  -X, --xml            output results in XML format\n");

            fputs(STDOUT, "\nTypes:\n");
            fputs(STDOUT, "You can specify a subset of roles/file-types to be ");
            fputs(STDOUT, "listed in the report.\n");
            fputs(STDOUT, "These roles are those as supported ");
            fputs(STDOUT, "by the PEAR installer.\n");
            fputs(STDOUT, "These are: data, doc, ext, php, script, src, test\n");

            fputs(STDOUT, "\nExamples:
                $ {$app} --all
                $ {$app} Console_Table
                $ {$app} -ttest,doc Console_Table
                $ {$app} --type=test,doc,php -h Console_Table Date_Holidays\n");
        }
    }

    /**
     * run
     *
     * @param mixed $options optional array of options as returned by getopt
     *
     * @see Console_Table
     *
     * @access public
     * @return integer
     */
    public function run($options = null)
    {
        if (php_sapi_name() !== 'cli') {
            echo  "cli version of php required.\n";
            return self::PEAR_SIZE_NON_CLI;
        }

        $pearsize = new PEAR_Size();
        $pearsize->setOutputDriver('text');
        $channels = array();

        if ($options === null) {
            $argv         = Console_Getopt::readPHPArgv();
            $long_options = array(
                    'all',
                    'allchannels',
                    'channel==',
                    'csv',
                    'help',
                    'human-readable',
                    'fsort',
                    'si',
                    'summarise',
                    'type==',
                    'verbose',
                    'version',
                    'xml',
                    );
            //determine which options are being used.
            $options = Console_Getopt::getopt($argv,
                                              "aAc:ChHt:SsVvX",
                                              $long_options);
        }

        if (PEAR::isError($options)) {
            self::usage($options);
            return self::PEAR_SIZE_INVALID_OPTIONS;
        }
        if (empty($options[0]) && empty($options[1])) {
            self::usage();
            return self::PEAR_SIZE_INVALID_OPTIONS;
        }

        ini_set('max_execution_time', 0);
        foreach ($options[0] as $opt) {
            $argument = str_replace('-', '', $opt[0]);
            switch ($argument) {
            case 'version':
            case 'V':
                echo self::APP_VERSION, "\n";
                return self::PEAR_SIZE_OK;
            case 'help':
                self::usage();
                return self::PEAR_SIZE_OK;
            }
        }
        try {
            $pearsize->parseCLIOptions($options);
            $pearsize->analyse();
            $errors   = $pearsize->errors;
            $warnings = $pearsize->warnings;
            if ((sizeof($errors) > 0) || (sizeof($warnings) > 0)) {
                $merged = array_merge($errors, $warnings);
                self::usage($merged);
                if (sizeof($errors) > 0) {
                    return 1;
                }
            }
            $pearsize->generateReport();
        }
        catch (PEAR_Size_Exception $e) {
            self::usage($e->getMessage());
            return 1;
        }
    }

}
?>
