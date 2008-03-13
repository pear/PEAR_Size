<?php
/**
 * PEAR_Size
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

/**
 * Require PEAR Class
 */
require_once 'PEAR.php';

/**
 * Need to determine PEAR configs
 */
require_once 'PEAR/Config.php';
/**
 * Working with the PEAR registry regarding installed packages
 */
require_once 'PEAR/Registry.php';

require 'PEAR/Size/Factory.php';

/**
 * PEAR_Size
 *
 * @category  PEAR
 * @package   PEAR_Size
 * @author    Ken Guest <ken@linux.ie>
 * @copyright 2008 Ken Guest
 * @license   LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PEAR_Size
 */
class PEAR_Size
{
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
     * Analyse packages associated with specified channel
     *
     * Return stats array (and total size used by all packages inside a
     * specified channel).
     *
     * @param array  $packages      array of names of packages to search forr.
     * @param object $reg           PEAR Registry object.
     * @param int    $channel_index index value of entry in channels_full array to
     *                              search for packages
     *
     * @return array
     */
    private function _analysePackages($packages, $reg, $channel_index)
    {

        $index = $channel_index;
        $stats = array();

        $channel_total = 0;

        foreach ($packages as $package) {
            if (strlen($package) > $this->name_length) {
                $this->name_length = strlen($package);
            }
            $sizes = array('data'=>0, 'doc'=>0, 'script'=>0, 'php'=>0, 'test'=>0);
            $pkg   = $this->reg->getPackage($package, $this->channels_full[$index]);
            if ($pkg === null) {
                array_push($this->errors, "Package $package not found");
                continue;
            }
            $version  = $pkg->getVersion();
            $filelist = $pkg->getFileList();

            $package_total = 0;

            foreach ($filelist as $file) {
                $role  = $file['role'];
                $srole = "|{$role}|";
                if (strpos($this->search_roles, $srole) !== false) {
                    $installed_location = $file['installed_as'];

                    $fsize = filesize($installed_location);

                    $sizes[$role]  += $fsize;
                    $package_total += $fsize;
                }
            }
            array_push($stats, array('package'=>$pkg->getName(),
                        'total'=>$package_total,
                        'sizes'=>$sizes));
            $channel_total += $package_total;
        }
        $this->grand_total += $channel_total;
        return array($stats, $channel_total);
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
        $table         = $this->_driver->table();
        $content_added = false;
        foreach ($stats as $statistic) {
            $content   = array();
            $content[] = $statistic['package'];
            $content[] = $this->_readableLine($statistic['total'],
                                              $this->readable,
                                              $this->round);
            if ($this->verbose) {
                $line = '';
                foreach ($details as $detail) {
                    $line .= "$detail: ";
                    $line .= $this->_readableLine($statistic['sizes'][$detail],
                            $this->readable,
                            $this->round);
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

    /**
     * class constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->config       = PEAR_Config::singleton();
        $this->reg          = $this->config->getRegistry();
        $this->channels     = array();
        $this->all          = false;
        $this->all_channels = false;
        $this->verbose      = false;
        $this->readable     = false;
        $this->search_roles = '|data|doc|php|script|test|';
        $this->sort_size    = false;
        $this->round        = false;
        $this->channel      = array();
        $this->errors       = array();

        $channels_full      = array();
        $channels_alias     = array();
        $channels_specified = array();
        $default_index      = null;

        $reg_channels = $this->reg->getChannels();
        $num_channels = sizeof($reg_channels);

        for ($i=0; $i < $num_channels; $i++) {
            $channel = $reg_channels[$i];

            $fullname           = $channel->getName();
            $channels_full[$i]  = $fullname;
            $channels_alias[$i] = $channel->getAlias();
            if ($fullname == PEAR_CONFIG_DEFAULT_CHANNEL) {
                $this->default_index = $i;
            }
        }
        $this->channel        = array();
        $this->channels_full  = $channels_full;
        $this->channels_alias = $channels_alias;
        $this->reg_channels   = $reg_channels;

    }

    /**
     * set up driver to be used, dependant on specified type.
     *
     * @param string $type name the type of driver (html, text...)
     *
     * @return void
     */
    public function setOutputDriver($type)
    {
        //get the factory
        $factory = new PEAR_Size_OutputFactory();
        //and create the driver...
        $this->_driver = $factory->createInstance($type);
    }

    /**
     * set verbosity level
     *
     * Should generated data (and the associated report) contain more information?
     *
     * @param bool $value defaults to true.
     *
     * @return void
     */
    public function setVerbose($value = true)
    {
        $this->verbose = $value;
    }

    /**
     * human readable...
     *
     * Should values be presented in a form more readable to mere mortals?
     *
     * @param bool $value defaults to true.
     *
     * @return void
     */
    public function setHumanReadable($value = true)
    {
        $this->readable = $value;
    }

    /**
     * Indicate whether results should be sorted by size.
     *
     * @param bool $value defaults to true.
     *
     * @return void
     */
    public function setSortSize($value = true)
    {
        $this->sort_size = $value;
    }

    /**
     * Indicate whether all packages in a channel should be analysed.
     *
     * @param bool $value defaults to true.
     *
     * @return void
     */
    public function setAll($value = true)
    {
        $this->all = $value;
    }

    /**
     * set variables for analysing all channels.
     *
     * @return void
     */
    public function setAllChannels()
    {
        $this->all_channels = true;
        $this->setAll();
        $this->channel = $this->channels_full;
    }

    /**
     * Should values be rounded to multiples of 1000?
     *
     * @param bool $value defaults to true
     *
     * @return void
     */
    public function setRoundValues($value = true)
    {
        $this->round = $value;

    }

    /**
     * set which channel is to be used
     *
     * @param string $channel_name Either full or alias name of a channel
     *
     * @return void
     */
    public function setChannel($channel_name)
    {
        $channel_pos = array_search($channel_name, $this->channels_alias);
        if ($channel_pos === false) {
            $channel_pos = array_search($channel_name, $this->channels_full);
            if ($channel_pos === false) {
                array_push($this->errors,
                           "Channel \"$channel_name\" does not exist");
            }
        }
        if ($channel_pos) {
            $this->channel[$channel_pos] = $channel_name;
        }
    }

    /**
     * Set the types/roles of files that are to be searched for in each package.
     *
     * @param string $types comma seperated string containing names of types.
     *
     * @return void
     */
    public function setTypes($types='')
    {
        $specified_roles    = explode(",", $types);
        $this->search_roles = "|" . implode($specified_roles, "|") ."|";
    }

    /**
     * parse options determined by Console_Getopt
     *
     * @param array $options options specified (filled by Console_Getopt::getopt)
     *
     * @return mixed returns a PEAR_Error on failure
     */
    public function parseCLIOptions($options)
    {
        foreach ($options[0] as $opt) {
            $argument = str_replace('-', '', $opt[0]);
            $param    = $opt[1];
            switch ($argument) {
            case 'all':
            case 'a':
                $this->setAll();
                break;
            case 'allchannels':
            case 'A':
                $this->setAllChannels();
                break;
            case 'c':
                $this->setChannel(trim($param));
                break;
            case 'type':
            case 't':
                $this->setTypes($param);
                break;
            case 'human-readable':
            case 'h':
                $this->setHumanReadable();
                break;
            case 'si':
            case 'H':
                $this->setHumanReadable();
                $this->setRoundValues();
                break;
            case 'verbose':
            case 'v':
                $this->setVerbose();
                break;
            case 'S':
                $this->setSortSize();
                break;
            }
        }
        $this->options = $options;
        if (count($this->errors)) {
            return PEAR::raiseError($this->errors[0]);
        }
    }

    /**
     * main analysis method
     *
     * @return void
     */
    public function analyse()
    {

        $this->grand_total = 0;
        $this->name_length = 0;

        $this->stats  = array();
        $this->errors = array();

        if (sizeof($this->channel) == 0) {
            $default_channel =  $this->config->get('default_channel');
            //determine position
            $pos = array_search($default_channel, $this->channels_full);
            //insert name of default channel into the correct position
            //of the channel array.
            $this->channel[$pos] =  $default_channel;
        }

        if (!$this->all) {
            if (empty($this->options[1]) && !($this->all_channels)) {
                usage();
                exit(PEAR_SIZE_INVALID_OPTIONS);
            }
            foreach ($this->channel as $index=>$given) {
                $packages  = $this->options[1];
                $cposition = $this->channels_full[$index];
                //analyse
                $channel_stats[$cposition] = $this->_analysePackages($packages,
                                                                     $this->reg,
                                                                     $index);
            }
        } else {
            foreach ($this->channel as $index=>$given) {
                $chanalias = $this->reg_channels[$index]->getAlias();
                $packages  = $this->reg->listPackages($chanalias);
                $cposition = $this->channels_full[$index];
                sort($packages);
                //analyse
                $channel_stats[$cposition] = $this->_analysePackages($packages,
                                                                     $this->reg,
                                                                     $index);
            }
        }
        $this->channel_stats = $channel_stats;
    }

    /**
     * generate the report
     *
     * @return void
     */
    public function generateReport()
    {
        $indices = substr($this->search_roles, 1, strlen($this->search_roles) - 2);
        $details = explode("|", $indices);

        $msg  = "Total: ";
        $msg .= $this->_readableLine($this->grand_total,
                $this->readable,
                $this->round);
        $this->_driver->display($msg);

        foreach ($this->channel_stats as $channel_name=>$ca) {
            list($stats, $channel_total) = $ca;
            $this->_driver->display("");
            $this->_driver->display("$channel_name:");
            $this->_driver->display(str_pad('', strlen($channel_name) + 1, "="));
            $msg = "Total: ";
            if ($this->readable) {
                $msg .= $this->_sizeReadable($channel_total, null, $this->round);
            } else {
                $msg .= $channel_total;
            }
            $this->_driver->display($msg);

            if ($this->sort_size) {
                usort($stats, array("PEAR_Size","_sortBySize"));
            }
            $this->_channelReport($stats, $details);
        }
    }
}
?>
