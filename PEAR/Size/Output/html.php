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
 * @version  Release: @PACKAGE_VERSION@
 * @link     http://pear.php.net/package/PEAR_Size
 */
class PEAR_Size_HTML_Table extends HTML_Table
{
    protected $rowcount = 0;

    /**
     * Constructor
     *
     * Set class of Table to "pear-size"
     */
    public function __construct()
    {
        parent::__construct(array("class" => "pear-size"));

    }

    /**
     * Add row to output
     *
     * @param string $contents data to add to row
     *
     * @return void
     */
    public function addRow($contents)
    {
        if ($this->rowcount % 2 == 0) {
            $class = "first";
        } else {
            $class = "second";
        }
        parent::addRow($contents, array("class" => $class));
        $this->rowcount++;
    }

    /**
     * Return table [in HTML]
     *
     * Return table - an alias for HTML_Table's toHTML method in an effort to
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
 * @version  Release: @PACKAGE_VERSION@
 * @link     http://pear.php.net/package/PEAR_Size
 */
class PEAR_Size_Output_Html extends PEAR_Size_Output_Driver
{
    /**
     * Display given text.
     *
     * @param string $text specified text to be displayed
     *
     * @return void
     */
    public function display($text)
    {
        echo $text;
        if ($text == "") {
            echo "<br/>";
        }
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
    /**
     * Generate the report
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
        $display_params
    ) {

        echo "<div class='pear-size'>\n";
        $indices = substr($search_roles, 1, strlen($search_roles) - 2);
        $details = explode("|", $indices);

        $this->_verbose    = $display_params["verbose"];
        $this->_readable   = $display_params["readable"];
        $this->_all_values = $display_params["all_values"];
        $this->_round      = $display_params["round"];
        $this->_summarise  = $display_params["summarise"];
        $this->_sort_size  = $display_params["sort_size"];

        //$stats, $details
        $indices = substr($search_roles, 1, strlen($search_roles) - 2);
        $details = explode("|", $indices);

        $msg  = "Total: ";
        $msg .= $this->readableLine(
            $grand_total,
            $this->_readable,
            $this->_round
        );
        $this->display($msg);

        foreach ($channel_stats as $channel_name=>$ca) {
            list($stats, $channel_total) = $ca;
            $this->display("");
            $this->display("<div class='channel-name'>$channel_name:</div>");
            $msg = "Total: ";
            if ($this->_readable) {
                $msg .= $this->_sizeReadable($channel_total, null, $this->_round);
            } else {
                $msg .= $channel_total;
            }
            $this->display("<div class='message'>$msg</div>");

            if ($this->_sort_size) {
                usort($stats, array("PEAR_Size_Output_Driver","_sortBySize"));
            }
            if (!$this->_summarise) {
                $this->_channelReport($stats, $details);
            }
        }
        echo "</div>\n";
    }
}
?>
