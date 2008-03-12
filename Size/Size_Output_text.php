<?php
/**
 * Text renderer for PEAR_Size component.
 *
 * PHP Version 5
 *
 * @category Size
 * @package  Size
 * @author   Ken Guest <ken@linux.ie>
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  CVS: <cvs_id>
 * @link     Size_Output_text.php
 */
require_once "Console/Table.php";
/**
 * PEAR_Size_Output_text
 *
 * @uses iPEAR_Size_Output_Driver
 * @category Size
 * @package  Size
 * @author   Ken Guest <ken@linux.ie>
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  CVS: <cvs_id>
 * @link     Size.php
 */
class PEAR_Size_Output_text implements PEAR_Size_Output_Driver
{
    /**
     * display given text.
     *
     * @param string $text text to be displayed
     *
     * @access public
     * @return void
     */
    public function display($text)
    {
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
    public function table()
    {
        $tbl = new Console_Table(CONSOLE_TABLE_ALIGN_LEFT);
        return $tbl;
    }
}
?>
