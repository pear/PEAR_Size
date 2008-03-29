<?php
/**
 * HTML renderer for PEAR_Size component.
 *
 * PHP Version 5
 *
 * @category  PEAR
 * @package   PEAR_Size
 * @author    Ken Guest <ken@linux.ie>
 * @copyright 2008 Ken Guest
 * @license   LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PEAR_Size
 */
require_once "HTML/Table.php";
/**
 * Extended form of HTML_Table
 *
 * @category PEAR
 * @package  PEAR_Size
 * @uses     HTML_Table
 * @author   Ken Guest <ken@linux.ie>
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PEAR_Size
 */
class PEAR_Size_HTML_Table extends HTML_Table
{

    /**
     * return table [in HTML]
     *
     * return table - an alias for HTML_Table's toHTML method in an effort to
     * have consistently named methods for components that do similar work.
     *
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
 * @category PEAR
 * @package  PEAR_Size
 * @uses     PEAR_Size_Output_Driver
 * @author   Ken Guest <ken@linux.ie>
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PEAR_Size
 */
class PEAR_Size_Output_Html extends PEAR_Size_Output_Driver
{
    /**
     * display given text.
     *
     * @param string $text specified text to be displayed
     *
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
     * @return void
     */
    public function table()
    {
        $tbl = new PEAR_Size_HTML_Table();
        return $tbl;
    }
}
?>
