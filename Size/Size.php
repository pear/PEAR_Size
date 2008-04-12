<?php
/**
 * PEAR_Size
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

/**
 * Exceptions
 */
require_once 'PEAR/Size/Exception.php';

/**
 * Use Factory to get instance of output driver
 */
require_once 'PEAR/Size/Factory.php';

/**
 * PEAR_Size: a class for determining the whole size that a package consumes on disk.
 *
 * @category  PEAR
 * @package   PEAR_Size
 * @author    Ken Guest <ken@linux.ie>
 * @copyright 2008 Ken Guest
 * @license   LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version   Release: @PACKAGE_VERSION@
 * @link      http://pear.php.net/package/PEAR_Size
 */
class PEAR_Size
{


    /**
     * true if all channels are to be analysed.
     *
     * @var bool
     */
    private $_all_channels = false;

    /**
     * true if all packages in set channel are to be analysed.
     *
     * @var bool $_all
     */
    private $_all = false;

    /**
     * channel[s] to analyse
     *
     * @var array
     */
    private $_channel = array();

    /**
     * results of analysis.
     *
     * @var array
     */
    private $_channel_stats = null;

    /**
     * array containing the alias name of each channel
     *
     * used for identifying channel which can be specified by either fullname
     * or by alias.
     *
     * @var array
     */
    private $_channels_alias = array();

    /**
     * array containing full names of all channels
     *
     * @var array
     */
    private $_channels_full = array();

    /**
     * PEAR_Config object
     *
     * @var $_config
     */
    private $_config = null;

    /**
     * location of default channel in reg_channels array
     *
     * @var int
     * @access protected
     */
    private $_default_index = null;

    /**
     * [report] output driver
     *
     * @var PEAR_Size_Output_Driver
     */
    private $_driver = null;

    /**
     * array or PEAR_Error of error messages
     *
     * @var array
     */
    public $errors = array();

    /**
     * array of warning messages.
     *
     * @var array
     * @access public
     */
    public $warnings = array();

    /**
     * Overall total filesize consumed by all channels
     *
     * @see _analysePackages()
     *
     * @var int
     */
    private $_grand_total = 0; //set by _analysePackages

    /**
     * track the longest name
     *
     * @var int $_name_length
     */
    private $_name_length = 0;

    /**
     * array of options as typically set by Console_Getopt::getopt
     *
     * @var array
     */
    private $_options = array();

    /**
     * present values in a readable form?
     *
     * Defaults to false
     *
     * @var bool
     */
    private $_readable = false;

    /**
     * list of channels returned by getChannels method.
     *
     * @var array $reg_channels
     */
    protected $reg_channels = array();

    /**
     * Registry object
     *
     * @var Registry $reg
     */
    protected $reg = null;

    /**
     * if true, round to SI values
     * @ var bool $_round
     */
    private $_round = false;

    /**
     * List of all valid roles to search for.
     *
     * May be extended by child class for cases where pear installer is embedded
     * and custom roles are in use.
     *
     * @var string $search_roles
     */
    protected $search_roles = '';

    /**
     * if true, sort results by size.
     *
     * Default to false.
     *
     * @var bool $_sort_size
     */
    private $_sort_size = false;

    /**
     * if true, don't display report of packages in channel - i.e. stick to summary.
     *
     * Default to false.
     *
     * @var bool $_summarise
     */
    private $_summarise = false;

    /**
     * If true, report contains more details re breakdown on a role-by-role basis.
     *
     * Default to false
     *
     * @var bool $_verbose
     */
    private $_verbose = false;


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
            if (strlen($package) > $this->_name_length) {
                $this->_name_length = strlen($package);
            }
            $sizes = array('data'   => 0,
                           'doc'    => 0,
                           'ext'    => 0,
                           'php'    => 0,
                           'script' => 0,
                           'src'    => 0,
                           'test'   => 0);
            $pkg   = $this->reg->getPackage($package, $this->_channels_full[$index]);
            if ($pkg === null) {
                array_push($this->warnings, "Package \"$package\" not found");
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
        $this->_grand_total += $channel_total;
        return array($stats, $channel_total);
    }

    /**
     * class constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->_config       = PEAR_Config::singleton();
        $this->reg           = $this->_config->getRegistry();
        $this->_channels     = array();
        $this->_all          = false;
        $this->_all_channels = false;
        $this->_verbose      = false;
        $this->_readable     = false;
        $this->search_roles  = '|data|doc|ext|php|script|src|test|';
        $this->_sort_size    = false;
        $this->_round        = false;
        $this->_channel      = array();
        $this->errors        = array();

        $channels_full  = array();
        $channels_alias = array();
        $_default_index = null;

        $reg_channels = $this->reg->getChannels();
        $num_channels = sizeof($reg_channels);

        for ($i=0; $i < $num_channels; $i++) {
            $channel = $reg_channels[$i];

            $fullname           = $channel->getName();
            $channels_full[$i]  = $fullname;
            $channels_alias[$i] = $channel->getAlias();
            if ($fullname == PEAR_CONFIG_DEFAULT_CHANNEL) {
                $this->_default_index = $i;
            }
        }
        $this->_channel        = array();
        $this->_channels_full  = $channels_full;
        $this->_channels_alias = $channels_alias;
        $this->reg_channels    = $reg_channels;

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
        $this->_verbose = $value;
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
        $this->_readable = $value;
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
        $this->_sort_size = $value;
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
        $this->_all = $value;
    }

    /**
     * set variables for analysing all channels.
     *
     * @param bool $mode defaults to true.
     *
     * @return void
     */
    public function setAllChannels($mode = true)
    {
        if ($mode) {
            $this->_all_channels = true;
            $this->setAll();
            $this->_channel = $this->_channels_full;
        } else {
            $this->_all_channels = false;
            $this->setAll(false);
            $this->_channel = array();
        }
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
        $this->_round = $value;
    }

    /**
     * Should a summary view be displayed rather than the detailed one?
     *
     * @param bool $value defaults to true
     *
     * @return void
     */
    public function setSummarise($value = true)
    {
        $this->_summarise = $value;
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
        if ($this->_all_channels) {
            $this->setAllChannels(false);
        }
        $channel_pos = array_search($channel_name, $this->_channels_alias);
        if ($channel_pos === false) {
            $channel_pos = array_search($channel_name, $this->_channels_full);
            if ($channel_pos === false) {
                array_push($this->errors,
                           "Channel \"$channel_name\" does not exist");
            }
        }
        if ($channel_pos !== false) {
            $this->_channel[$channel_pos] = $channel_name;
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
        if (sizeof($options) != 2) {
            $msg = "Options array wrong size - must be a two element array.";
            throw new PEAR_Size_Exception($msg);
        }
        foreach ($options[0] as $opt) {
            $argument = preg_replace("/^--/", '', $opt[0]);
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
            case 'C':
            case 'csv':
                $this->setOutputDriver('csv');
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
            case 'fsort':
            case 'S':
                $this->setSortSize();
                break;
            case 'summarise':
            case 's':
                $this->setSummarise();
                break;
            case 'X':
            case 'xml':
                $this->setOutputDriver('xml');
                break;
            }
        }
        $this->_options = $options;
        if (count($this->errors)) {
            throw new PEAR_Size_Exception($this->errors[0]);
        }
    }

    /**
     * main analysis method
     *
     * @return void
     */
    public function analyse()
    {

        $this->_grand_total = 0;
        $this->_name_length = 0;

        $this->stats  = array();
        $this->errors = array();

        $mixed = false;
        foreach ($this->_options[1] as $name) {
            if (strpos($name, "/") !== false) {
                $mixed = true;
            }
        }

        if (sizeof($this->_channel) == 0) {
            $default_channel =  $this->_config->get('default_channel');
            //determine position
            $pos = array_search($default_channel, $this->_channels_full);
            //insert name of default channel into the correct position
            //of the channel array.
            $this->_channel[$pos] =  $default_channel;
        }

        if ($mixed) {
            // if at least one package is specified in mixed
            // form (channel/package) then iterate through all of them, etc etc.
            $search_for = array();
            foreach ($this->_options[1] as $name) {
                if (false === strpos($name, "/")) {
                    //if no channel is specified in this
                    //form, assume the default
                    $package = $name;
                    $channel = $default_channel;
                } else {
                    $temp    = explode("/", $name);
                    $channel = $temp[0];
                    $package = $temp[1];
                    $pos     = array_search($channel, $this->_channels_alias);
                    if (false !== $pos) {
                        $channel = $this->_channels_full[$pos];
                    }
                }
                $search_for[$channel][] = $package;
            }
            foreach ($search_for as $channel=>$packages) {
                $index = array_search($channel, $this->_channels_full);
                //analyse
                $channel_stats[$channel] = $this->_analysePackages($packages,
                                                                   $this->reg,
                                                                   $index);
            }
        } elseif (!$this->_all) {
            if (empty($this->_options[1]) && !($this->_all_channels)) {
                throw new PEAR_Size_Exception('usage');
            }
            foreach ($this->_channel as $index=>$given) {
                $packages  = $this->_options[1];
                $cposition = $this->_channels_full[$index];
                //analyse
                $channel_stats[$cposition] = $this->_analysePackages($packages,
                                                                     $this->reg,
                                                                     $index);
            }
        } else {
            foreach ($this->_channel as $index=>$given) {
                $chanalias = $this->reg_channels[$index]->getAlias();
                $packages  = $this->reg->listPackages($chanalias);
                $cposition = $this->_channels_full[$index];
                sort($packages);
                //analyse
                $channel_stats[$cposition] = $this->_analysePackages($packages,
                                                                     $this->reg,
                                                                     $index);
            }
        }
        $this->_channel_stats = $channel_stats;
    }

    /**
     * generate the report
     *
     * @return void
     */
    public function generateReport()
    {
        $display_params = array("verbose" => $this->_verbose,
                                "readable" => $this->_readable,
                                "sort_size" => $this->_sort_size,
                                "summarise" => $this->_summarise,
                                "round" => $this->_round);

        if (is_null($this->_driver)) {
            throw new PEAR_Size_Exception("Output driver not set.");
        }
        if (is_null($this->_channel_stats)) {
            throw new PEAR_Size_Exception("Channel Data not defined.");
        }
        $this->_driver->generateReport($this->_channel_stats,
                                       $this->search_roles,
                                       $this->_grand_total,
                                       $display_params);
    }
}
?>
