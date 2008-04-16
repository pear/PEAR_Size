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
 * Pull in PEAR_Command_Common to extend it
 */
require_once 'PEAR/Command/Common.php';

/**
 * Need to determine PEAR configs
 */
require_once 'PEAR/Config.php';

/**
 * the class of the hour
 */
require_once 'PEAR/Size.php';

/**
 * Use PEAR_Size_CLI to set up everything for us
 */
require_once 'PEAR/Size/CLI.php';


/**
 * PEAR_Command_Size: integrate PEAR_Size within the pear command line.
 *
 * @category  PEAR
 * @package   PEAR_Size
 * @author    Ken Guest <ken@linux.ie>
 * @copyright 2008 Ken Guest
 * @license   LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version   Release: @PACKAGE_VERSION@
 * @link      http://pear.php.net/package/PEAR_Size
 */
class PEAR_Command_Size extends PEAR_Command_Common
{
    var $commands = array(
        'size' => array(
            'summary'  => "Information on how much space a package requires.",
            "function" => "doSize",
            "shortcut" => "sz",
            "options"  => array(
                "all"          => array(
                    "shortopt" => "a",
                    "doc"      => "display information for all installed packages"),
                "allchannels"  => array(
                    "shortopt" => "A",
                    "doc"      => "list packages from all channels, not just the default one"),
                "channel"      => array (
                    "shortopt" => "c",
                    "arg"      => "CHANNEL",
                    "doc"      => "specify which channel"),
                "csv"          => array(
                    "shortopt" => "C",
                    "doc"      => "output results in CSV format (sizes are measured in bytes)."),
                "human-readable" => array (
                    "shortopt" => "h",
                    "doc"      => "print sizes in human readable format (for example: 492 B 1KB 7MB)"),
                "si"           => array(
                    "shortopt" => "H",
                    "doc"      => "likewise, but use powers of 1000 not 1024"),
                "type" => array(
                    "shortopt" => "t",
                    "arg"      => "TYPES",
                    "doc"      => "specify what type of files are required for the report"),
                "summarise"    => array(
                    "shortopt" => "s",
                    "doc"      => "display channel summary view"),
                "fsort" => array(
                    "shortopt" => "S",
                    "doc"      => "sort by file size"),
                "verbose" => array(
                    "shortopt" => "v",
                    "doc"      => "display more detailed information"),
                "version" => array(
                    "shortopt" => "V",
                    "doc"      => "output version information and exit"),
                    ),
                "xml"          => array(
                    "shortopt" => "X",
                    "doc"      => "output results in XML format"),

                              ));

    /**
     * doSize
     *
     * @param mixed $command instance of command object
     * @param mixed $options the command options
     * @param mixed $params  the command parameters (for the options)
     *
     * @access public
     * @return void
     */
    function doSize($command, $options, $params)
    {

        $altered = array();
        foreach ($options as $option=>$value) {
            $altered[] = array($option, $value);

        }
        $ar  = array($altered, $params);
        $cli = new PEAR_Size_CLI;
        $cli->setAppName('pear size');
        return ($cli->run($ar));
    }
}
?>
