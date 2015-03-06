<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.controllerform');

class PhocaPanoramaCpControllerPhocaPanoramacat extends JControllerForm
{
	protected	$option 		= 'com_phocapanorama';
	
	function __construct($config=array()) {
		parent::__construct($config);
	}
	
	protected function allowAdd($data = array()) {
		$user		= JFactory::getUser();
		$allow		= null;
		$allow	= $user->authorise('core.create', 'com_phocapanorama');
		if ($allow === null) {
			return parent::allowAdd($data);
		} else {
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id') {
		$user		= JFactory::getUser();
		$allow		= null;
		$allow	= $user->authorise('core.edit', 'com_phocapanorama');
		if ($allow === null) {
			return parent::allowEdit($data, $key);
		} else {
			return $allow;
		}
	}
	
	public function batch() {
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model	= $this->getModel('phocapanoramacat', '', array());
		$this->setRedirect(JRoute::_('index.php?option=com_phocapanorama&view=phocapanoramacats'.$this->getRedirectToListAppend(), false));
		return parent::batch($model);
	}
}
?>
