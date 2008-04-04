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
 * @version  CVS: $Id$
 * @link     pearsize.php
 *
 * $ pearsize --all
 * $ pearsize Console_Table
 * $ pearsize Console_Table --type=test,doc
*/
/**
 * Neded to parse options on command line
 */
require 'Console/Getopt.php';
if (is_file(dirname(__FILE__) . "/../Size/Size.php") === true) {
    include dirname(__FILE__) . "/../Size/Size.php";
} else {
    include 'PEAR/Size.php';
    include 'PEAR/Size/CLI.php';
}

$cli = new PEAR_Size_CLI;
exit($cli->run());
?>
