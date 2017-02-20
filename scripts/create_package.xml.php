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
 * Update package.xml automagically
 */
require_once 'PEAR/PackageFileManager2.php';

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$notes = "
* bug fix: Fix method name mismatch.
";

$version       = '1.0.0RC2';
$api_version   = '0.1.2';
$stability     = 'beta';
$api_stability = 'beta';


$packagedir  = dirname(__FILE__);
$packagefile = '../package.xml';

$options = array(
    'baseinstalldir' => 'PEAR',
    'version' => $version,
    'packagedirectory' => $packagedir,
    'filelistgenerator' => 'git',
    'notes' => $notes,
    'package' => 'PEAR_Size',
    'dir_roles' => array(
        'docs' =>'doc',
    ),

    'packagefile' => 'package.xml',
    'simpleoutput' => true,
    //'addhiddenfiles' => true,
    'clearcontents' => false,
    'changelogoldtonew' => false,
    'ignore' => array(__FILE__),
    );
//import specified options
$p2 = PEAR_PackageFileManager2::importOptions($packagefile, $options);
$p2->setReleaseVersion($version);
$p2->setReleaseStability($stability);
$p2->setAPIVersion($api_version);
$p2->setAPIStability($api_stability);
$p2->setLicense('LGPL', 'http://www.gnu.org/licenses/lgpl.html');
$p2->setNotes($notes);

if (isset($_GET['make'])
    || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')
) {
    $p2->writePackageFile();
} else {
    $p2->debugPackageFile();
}
?>
