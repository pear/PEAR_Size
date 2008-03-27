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
interface PEAR_Size_Output_Driver
{
    /**
     * display given text.
     *
     * @param string $text text to be displayed
     *
     * @return void
     */
    public function display($text);
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
 * @version   CVS: <cvs_id>
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
