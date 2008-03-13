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
 * @license  LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version  CVS: <cvs_id>
 * @link     Size_Factory.php
 */

interface PEAR_SIZE_Factory
{
    public function createInstance();
}
interface PEAR_Size_Output_Driver
{
    public function display($text);
}
/**
 * PEAR_SIZE_OutputFactory
 *
 * @category  Size_Factory
 * @package   Size_Factory
 * @uses      PEAR_SIZE_Factory
 * @author    Ken Guest <kguest@php.net>
 * @copyright 1997-2005 The PHP Group
 * @license   LGPL (see http://www.gnu.org/licenses/lgpl.html)
 * @version   CVS: <cvs_id>
 * @link      Size_Factory.php
 */
class  PEAR_SIZE_OutputFactory implements PEAR_SIZE_Factory
{
    /**
     * create required instance of the Output 'driver'.
     *
     * @param string $type type of instance required, lowercase.
     *
     * @access public
     * @return object
     */
    function createInstance($type = 'text')
    {
        include "PEAR/Size/Output_". $type . ".php";
        $class = "PEAR_Size_Output_". $type;
        return new $class();
    }
}
?>
