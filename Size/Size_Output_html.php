<?php
/**
 * HTML renderer for PEAR_Size component.
 *
 * PHP Version 5
 *
 * @category Size
 * @package  Size
 * @author   Ken Guest <ken@linux.ie>
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  CVS: <cvs_id>
 * @link     Size.php
 */
require_once "HTML/Table.php";
/**
 * Extended form of HTML_Table
 *
 * @uses    HTML_Table
 * @category Size
 * @package  Size
 * @author   Ken Guest <ken@linux.ie>
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  CVS: <cvs_id>
 * @link     Size_Output_html.php
 */
class PEAR_Size_HTML_Table extends HTML_Table
{

    /**
     * return table [in HTML]
     *
     * return table - an alias for HTML_Table's toHTML method in an effort to
     * have consistently named methods for components that do similar work.
     *
     * @access public
     * @return void
     */
    public function getTable()
    {
        return $this->toHtml();
    }
}
/**
 * PEAR_Size_Output_html
 *
 * @uses PEAR_Size_Output_Driver
 * @category Size
 * @package  Size
 * @author   Ken Guest <ken@linux.ie>
 * @license  GPL (see http://www.gnu.org/licenses/gpl.txt)
 * @version  CVS: <cvs_id>
 * @link     Size_Output_html.php
 */
class PEAR_Size_Output_html implements PEAR_Size_Output_Driver
{
    /**
     * display given text.
     *
     * @param string $text specified text to be displayed
     *
     * @access public
     * @return void
     */
    public function display($text)
    {
        echo $text, "<br/>\n";
    }
    /**
     * Return extended HTML_Table object
     *
     * Used for displaying detail lines of the report in tabular format.
     *
     * @access public
     * @return void
     */
    public function table()
    {
        $tbl = new PEAR_Size_HTML_Table();
        return $tbl;
    }
}
?>
