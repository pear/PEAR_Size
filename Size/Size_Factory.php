<?php
/**
 * Size_Factory.php
 * 07-Jan-2008
 *
 * PHP Version 5
 *
 * @category Size_Factory
 * @package  Size_Factory
 * @author   Ken Guest <ken@guest.cx>
 * @license  GPL (see http://www.gnu.org/licenses/gpl.txt)
 * @version  CVS: <cvs_id>
 * @link     Size_Factory.php
 * @todo
*/

interface iPEAR_SIZE_Factory
{
    public function createInstance();
}
interface iPEAR_Size_Output_Driver
{
    public function display($text);
}
class  PEAR_SIZE_OutputFactory implements iPEAR_SIZE_Factory
{
    function createInstance($type = 'text')
    {
        include "PEAR/Size/Output_". $type . ".php";
        $class = "PEAR_Size_Output_". $type;
        return new $class();
    }
}
?>
