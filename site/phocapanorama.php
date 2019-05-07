<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JPATH_COMPONENT.'/controller.php' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocapanorama/helpers/phocapanoramautils.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocapanorama/helpers/phocapanorama.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocapanorama/helpers/html/ordering.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocapanorama/helpers/route.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocapanorama/helpers/pagination.php' );


// Require specific controller if requested
if($controller = JFactory::getApplication()->input->getWord('controller')) {
    $path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

$classname    = 'PhocaPanoramaController'.ucfirst($controller);
$controller   = new $classname( );
$controller->execute( JFactory::getApplication()->input->get('task') );
$controller->redirect();
?>
