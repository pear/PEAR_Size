<?php
require_once 'PEAR/PackageFileManager2.php';

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$notes = "
* foobar:  bing bong
* request: channel summary
* request: CSV output
* request: XML output
* request: console/text output is neater
* bug fix: #13549 - all PEAR roles are supported (including 'ext' and 'src')
";
$version         = '0.1.2';
$api_version     = '0.1.2';
$stability       = 'alpha';
$api_stability   = 'alpha';


$packagedir = dirname(__FILE__);
$packagefile = '../package.xml';

$options = array(
    'baseinstalldir' => 'PEAR',
    'version' => $version,
    'packagedirectory' => $packagedir,
    'filelistgenerator' => 'cvs',
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
$p2 = PEAR_PackageFileManager2::importOptions($packagefile, $options);
$p2->setReleaseVersion($version);
$p2->setReleaseStability($stability);
$p2->setAPIVersion($api_version);
$p2->setAPIStability($api_stability);
$p2->setLicense('LGPL', 'http://www.gnu.org/licenses/lgpl.html');
$p2->setNotes($notes);

if (isset($_GET['make'])
    || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p2->writePackageFile();
} else {
    $p2->debugPackageFile();
}
?>
