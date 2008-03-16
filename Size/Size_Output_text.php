<?php
/**
 * Text renderer for PEAR_Size component.
 *
 * PHP Version 5
 *
 * @category PEAR
 * @package  PEAR_Size
 * @author   Ken Guest <ken@linux.ie>
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  CVS: <cvs_id>
 * @link     http://pear.php.net/package/PEAR_Size
 */
require_once "Console/Table.php";
/**
 * Output info as pure text.
 *
 * @category PEAR
 * @package  PEAR_Size
 * @uses     iPEAR_Size_Output_Driver
 * @author   Ken Guest <ken@linux.ie>
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  CVS: <cvs_id>
 * @link     http://pear.php.net/package/PEAR_Size
 */
class PEAR_Size_Output_text implements PEAR_Size_Output_Driver
{
    /**
     * display given text.
     *
     * @param string $text text to be displayed
     *
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
     * @return void
     */
    public function table()
    {
        $tbl = new Console_Table(CONSOLE_TABLE_ALIGN_LEFT);
        return $tbl;
    }
}
?>
