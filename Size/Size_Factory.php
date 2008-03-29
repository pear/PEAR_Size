<?php
/**
 * Size_Factory.php
 * 07-Jan-2008
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
 * Factory object must contract to having a createInstance method
 *
 * @category  PEAR
 * @package   PEAR_Size
 * @author    Ken Guest <ken@linux.ie>
 * @copyright 2008 Ken Guest
 * @license   LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PEAR_Size
 */
interface PEAR_SIZE_Factory
{
    /**
     * create required instance of the Output 'driver'.
     *
     * @return object
     */
    public function createInstance();
}

/**
 * An Output Driver is used to display information/text in a manner
 * specific to that driver.
 *
 * @category  PEAR
 * @package   PEAR_Size
 * @author    Ken Guest <ken@linux.ie>
 * @copyright 2008 Ken Guest
 * @license   LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PEAR_Size
 */
class PEAR_Size_Output_Driver
{
    /**
     * Return either given value, or it in readable form depending on criteria.
     *
     * @param integer $value    value
     * @param boolean $readable human readable form?
     * @param boolean $round    round to values of 1000 rather than 1024?
     *
     * @return string
     */
    private function _readableLine($value, $readable, $round)
    {
        if ($readable) {
            return $this->_sizeReadable($value, null, $round);
        } else {
            return (string) $value;
        }
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

        $indices = substr($this->search_roles, 1, strlen($this->search_roles) - 2);
        $details = explode("|", $indices);

        $this->_verbose  = $display_params["verbose"];
        $this->_readable = $display_params["readable"];
        $this->_round    = $display_params["round"];

        //$stats, $details
        $indices = substr($search_roles, 1, strlen($search_roles) - 2);
        $details = explode("|", $indices);

        $msg  = "Total: ";
        $msg .= $this->_readableLine($grand_total,
                $this->_readable,
                $this->_round);
        $this->display($msg);

        foreach ($channel_stats as $channel_name=>$ca) {
            list($stats, $channel_total) = $ca;
            $this->display("");
            $this->display("$channel_name:");
            $this->display(str_pad('', strlen($channel_name) + 1, "="));
            $msg = "Total: ";
            if ($this->_readable) {
                $msg .= $this->_sizeReadable($channel_total, null, $this->_round);
            } else {
                $msg .= $channel_total;
            }
            $this->display($msg);

            if ($this->_sort_size) {
                usort($stats, array("PEAR_Size","_sortBySize"));
            }
            if (!$this->_summarise) {
                $this->_channelReport($stats, $details);
            }
        }
    }

    /**
     * Display report of packages in a channel
     *
     * @param array $stats   array of statistics
     * @param mixed $details additional details
     *
     * @return void
     */
    private function _channelReport($stats, $details)
    {
        $table         = $this->table();
        $content_added = false;
        foreach ($stats as $statistic) {
            $content   = array();
            $content[] = $statistic['package'];
            $content[] = $this->_readableLine($statistic['total'],
                    $this->_readable,
                    $this->_round);
            if ($this->_verbose) {
                $line = '';
                foreach ($details as $detail) {
                    $line .= "$detail: ";
                    $line .= $this->_readableLine($statistic['sizes'][$detail],
                            $this->_readable,
                            $this->_round);
                    $line .= "; ";
                }
                $line      = substr($line, 0, strlen($line) - 2);
                $content[] = "($line)";
            }
            if ($content !== array() ) {
                $table->addRow($content);
                $content_added = true;
            }
        }
        if ($content_added) {
            echo $table->getTable();
        }
    }

}

/**
 * Use Factory pattern to give a output driver object.
 *
 * @category  PEAR
 * @package   PEAR_Size
 * @uses      PEAR_SIZE_Factory
 * @author    Ken Guest <ken@linux.ie>
 * @copyright 2008 Ken Guest
 * @license   LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PEAR_Size
 */
class  PEAR_SIZE_OutputFactory implements PEAR_SIZE_Factory
{
    /**
     * create required instance of the Output 'driver'.
     *
     * @param string $type type of instance required, lowercase. 'text' by default.
     *
     * @return object
     */
    public function createInstance($type = 'text')
    {
        include "PEAR/Size/Output_". $type . ".php";
        $class = "PEAR_Size_Output_". $type;
        return new $class();
    }
}
?>
