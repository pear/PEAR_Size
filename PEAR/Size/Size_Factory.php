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
 * @version   Release: @PACKAGE_VERSION@
 * @link      http://pear.php.net/package/PEAR_Size
 */
interface PEAR_Size_Factory
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
 * @version   Release: @PACKAGE_VERSION@
 * @link      http://pear.php.net/package/PEAR_Size
 */
class PEAR_Size_Output_Driver
{
    /**
     * Used for sorting stats array.
     *
     * @param array $a stats array entry
     * @param array $b stats array entry
     *
     * @return int -1 if total in $a is less than $b, else 1.
     */
    private function _sortBySize($a, $b)
    {
        $a_total = $a['total'];
        $b_total = $b['total'];
        if ($a_total === $b_total) {
            return 0;
        }
        return ($a_total < $b_total ? -1 : 1);
    }

    /**
     * Determine a more readable form of the given size.
     *
     * @param int     $size      size value
     * @param string  $retstring formatting string; null by default.
     * @param boolean $round     round to multiples of 1000? false by default.
     *
     * @return string
     */
    static function _sizeReadable($size, $retstring = null, $round = false)
    {
        //adapted from Public Domain licensed code at
        //http://aidanlister.com/repos/v/function.size_readable.php
        $sizes  = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = 1024;
        if ($round) {
            $factor = 1000;
        }
        if ($retstring === null) {
            $retstring = '%01.2f %s';
        }
        $lastsizestring = end($sizes);
        foreach ($sizes as $sizestring) {
            if ($size < $factor) {
                break;
            }
            if ($sizestring !== $lastsizestring) {
                $size /= $factor;
            }
        }
        if ($sizestring === $sizes[0]) {
            $retstring = '%01d %s';
        } elseif ($sizestring === 'KB' && $round) {
            $sizestring = 'kB';
        }
        return sprintf($retstring, $size, $sizestring);
    }
    /**
     * Return either given value, or it in readable form depending on criteria.
     *
     * @param integer $value    value
     * @param boolean $readable human readable form?
     * @param boolean $round    round to values of 1000 rather than 1024?
     *
     * @return string
     */
    protected function _readableLine($value, $readable, $round)
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
                usort($stats, array("PEAR_Size_Output_Driver","_sortBySize"));
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
    protected function _channelReport($stats, $details)
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
                    $value = $this->_readableLine($statistic['sizes'][$detail],
                            $this->_readable,
                            $this->_round);
                    if (($this->_all_values) or ($value > 0)) {
                        $line .= "$detail: ";
                        $line .= $this->_readableLine($statistic['sizes'][$detail],
                                $this->_readable,
                                $this->_round);
                        $line .= "; ";
                    }
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
 * @uses      PEAR_Size_Factory
 * @author    Ken Guest <ken@linux.ie>
 * @copyright 2008 Ken Guest
 * @license   LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version   Release: @PACKAGE_VERSION@
 * @link      http://pear.php.net/package/PEAR_Size
 */
class  PEAR_Size_OutputFactory implements PEAR_Size_Factory
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
        $driverfile = "PEAR/Size/Output_". $type . ".php";
        $paths      = explode(":", ini_get("include_path"));
        $result     = false;

        while ((!($result)) && (list($key,$val) = each($paths))) {
            $result = @file_exists($val . "/" . $driverfile);
        }
        if ($result) {
            include $driverfile;
            $class = "PEAR_Size_Output_". $type;
            return new $class();
        } else {
            throw new PEAR_Size_Exception("Output driver " .
                                          "$driverfile could not be found.");
        }
    }
}
?>
