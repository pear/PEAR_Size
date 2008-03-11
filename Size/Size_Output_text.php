<?php
/**
 * Text renderer for PEAR_Size component.
 *
 * PHP Version 5
 *
 * @category Size
 * @package  Size
 * @author   Ken Guest <ken@linux.ie>
 * @license  GPL (see http://www.gnu.org/licenses/gpl.txt)
 * @version  CVS: <cvs_id>
 * @link     Size.php
 */
require_once("Console/Table.php");
class PEAR_Size_Output_text implements iPEAR_Size_Output_Driver
{
    /**
     * display given text.
     *
     * @param string $text
     *
     * @access public
     * @return void
     */
    public function display($text) {
        echo $text, "\n";
    }
    /**
     * Return Console_Table object
     *
     * Used for displaying detail lines of the report in tabular format.
     *
     * @access public
     * @return void
     */
    public function table() {
        //if possible, turn off the ascii art
        if (defined('CONSOLE_TABLE_BORDER_OFF')) {
            $tbl = new Console_Table(CONSOLE_TABLE_ALIGN_LEFT, CONSOLE_TABLE_BORDER_OFF);
        } else {
            $tbl = new Console_Table(CONSOLE_TABLE_ALIGN_LEFT);
        }
        return $tbl;
    }
}
?>
