<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

$this->t['action'] = str_replace('&amp;', '&', $this->t['action']);
$this->t['action'] = htmlspecialchars($this->t['action']);

echo '<div class="clearfix"></div>';
echo '<form action="'.$this->t['action'].'" method="post" name="adminForm">'. "\n";
echo '<div class="pagination row">';
if ($this->t['p']->get('show_pagination')) {	
	echo '<div class="col-xs-12 col-sm-12 col-md-12" style="text-align:center;padding:0;margin:0">';
	
	echo '<div>'. $this->t['pagination']->getPagesLinks() . '</div>';
	
	echo '<div>';
	if ($this->t['p']->get('show_pagination_limit')) {
		echo JText::_('COM_PHOCAPANORAMA_DISPLAY_NUM') .'&nbsp;' .$this->t['pagination']->getLimitBox();
	}
	echo ' &nbsp;'.$this->t['pagination']->getPagesCounter();
	echo '</div>';
	echo '</div>';
}
echo '</div>';
echo JHTML::_( 'form.token' );
echo '</form>';
?>