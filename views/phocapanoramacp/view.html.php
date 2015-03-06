<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pane' );

class PhocaPanoramaCpViewPhocaPanoramaCp extends JViewLegacy
{
	protected $t;
	protected $views;
	
	function display($tpl = null) {
		
		$this->t	= PhocaPanoramaUtils::setVars();
		$this->views= array(
		'items'		=> $this->t['l'] . '_ITEMS',
		'cats'		=> $this->t['l'] . '_CATEGORIES',
		'info'		=> $this->t['l'] . '_INFO'
		);
		
		JHTML::stylesheet( $this->t['s'] );
		JHTML::_('behavior.tooltip');
		$class	= $this->t['n'] . 'Utils';
		$this->t['version'] = $class::getExtensionVersion();
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		require_once JPATH_COMPONENT.'/helpers/'.$this->t['c'].'cp.php';
		$class	= $this->t['n'] . 'CpHelper';
		$canDo	= $class::getActions($this->t['c']);
		JToolBarHelper::title( JText::_( $this->t['l'].'_PP_CONTROL_PANEL' ), 'home-2 cpanel' );
		
		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = JToolBar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocapanorama" class="btn btn-small"><i class="icon-home-2" title="'.JText::_($this->t['l'].'_CONTROL_PANEL').'"></i> '.JText::_($this->t['l'].'_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences($this->t['o']);
			JToolBarHelper::divider();
		}
		JToolBarHelper::help( 'screen.'.$this->t['c'], true );
	}
}
?>