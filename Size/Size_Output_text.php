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
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/PEAR_Size
 */
require_once "Console/Table.php";
/**
 * Output info as pure text.
 *
 * @category PEAR
 * @package  PEAR_Size
 * @uses     PEAR_Size_Output_Driver
 * @author   Ken Guest <ken@linux.ie>
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  Release: @PACKAGE_VERSION@
 * @link     http://pear.php.net/package/PEAR_Size
 */
class PEAR_Size_Output_Text extends PEAR_Size_Output_Driver
{

    /**
     * Filter callback for Console_Table - limit name column to a certain length
     *
     * @param string $data Content for the Name column
     *
     * @return string
     */
    function _splitName($data)
    {
        if (strlen($data) <= 42) {
            $value = str_pad($data, 42);
        } else {
            $value = '...' . substr($data, (strlen($data) - 39));
        }
        return $value;
    }

    /**
     * Filter callback for Console_Table - limit size column to a certain length
     *
     * @param string $data Content for the Size column
     *
     * @return string
     */
    function _splitSize($data)
    {
        return str_pad($data, 8, ' ');
    }

    /**
     * Filter callback for Console_Table - limit detail column to a certain length
     *
     * @param string $data Content for the Detail column
     *
     * @return string
     */
    function _splitDetail($data)
    {
        $str = str_replace('(', '', $data);
        $str = str_replace(')', '', $str);
        $str = str_replace('; ', "\r\n", $str);
        $str = rtrim($str, "\r\n");
        return $str;
    }

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
        #$tbl = new Console_Table(CONSOLE_TABLE_ALIGN_LEFT);
        $tbl = new Console_Table(CONSOLE_TABLE_ALIGN_LEFT, " ", 0);
        //set up filters for limiting widths of columns inside the table
        $filter0 = array($this, '_splitName');
        $tbl->addFilter(0, $filter0);
        $filter1 = array($this, '_splitSize');
        $tbl->addFilter(1, $filter1);
        if ($this->cols == 3) {
            $filter2 = array($this, '_splitDetail');
            $tbl->addFilter(2, $filter2);
        }
        return $tbl;
    }
    /**
     * generate the report
     *
     * @param array $channel_stats  contains statistics for each channel
     * @param array $search_roles   roles searched for
     * @param array $grand_total    entire total of disk space consumed by channel
     * @param array $display_params parameters relevant to display of report.
     *
     * @return void
     */
    public function generateReport($channel_stats,
                                   $search_roles,
                                   $grand_total,
                                   $display_params)
    {
        if ($display_params["verbose"]) {
            $this->cols = 3;
        } else {
            $this->cols = 2;
        }
        parent::generateReport($channel_stats,
                               $search_roles,
                               $grand_total,
                               $display_params);
    }
}
?>
