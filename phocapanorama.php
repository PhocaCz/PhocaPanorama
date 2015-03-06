<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if (!JFactory::getUser()->authorise('core.manage', 'com_phocapanorama')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once( JPATH_COMPONENT.'/controller.php' );
jimport( 'joomla.filesystem.folder' ); 
jimport( 'joomla.filesystem.file' );
require_once( JPATH_COMPONENT.'/helpers/phocapanoramautils.php' );
require_once( JPATH_COMPONENT.'/helpers/phocapanorama.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadmin.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminview.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminviews.php' );
require_once( JPATH_COMPONENT.'/helpers/html/batch.php' );


jimport('joomla.application.component.controller');
$controller	= JControllerLegacy::getInstance('PhocaPanoramaCp');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>