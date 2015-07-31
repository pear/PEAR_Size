<?php
/**
 * CSV renderer for PEAR_Size component.
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
 * Output info in Comma Seperated Value style.
 *
 * @category PEAR
 * @package  PEAR_Size
 * @uses     PEAR_Size_Output_Driver
 * @author   Ken Guest <ken@linux.ie>
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  Release: @PACKAGE_VERSION@
 * @link     http://pear.php.net/package/PEAR_Size
 */
class PEAR_Size_Output_CSV extends PEAR_Size_Output_Driver
{
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
        $indices = substr($search_roles, 1, strlen($search_roles) - 2);
        $details = explode("|", $indices);
        echo "channel,package,total,", str_replace("|", ",", $indices), "\n";

        foreach ($channel_stats as $channel_name=>$ca) {
            list($stats, $channel_total) = $ca;
            foreach ($stats as $statistic) {
                $content   = array();
                $content[] = $channel_name;
                $content[] = $statistic['package'];
                $content[] = $statistic['total'];
                foreach ($details as $detail) {
                    $content[] = $statistic['sizes'][$detail];
                }
                echo implode($content, ","), "\n";
            }
        }
    }
}


?>
