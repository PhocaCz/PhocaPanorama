<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

if (! class_exists('PhocaPanoramaCategory')) {
    require_once( JPATH_ADMINISTRATOR.'/components/com_phocapanorama/helpers/phocapanorama.php');
}

class JFormFieldPhocaPanoramaCategory extends JFormField
{
	protected $type 		= 'PhocaPanoramaCategory';

	protected function getInput() {

		$db = JFactory::getDBO();

       //build the list of categories
		$query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
		. ' FROM #__phocapanorama_categories AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$data = $db->loadObjectList();

		// TODO - check for other views than category edit
		$view 	= JFactory::getApplication()->input->get( 'view' );
		$catId	= -1;
		if ($view == 'phocapanoramacat') {
			$id 	= $this->form->getValue('id'); // id of current category
			if ((int)$id > 0) {
				$catId = $id;
			}
		}
		/*if ($view == 'phocapanoramafile') {
			$id 	= $this->form->getValue('catid'); // id of current category
			if ((int)$id > 0) {
				$catId = $id;
			}
		}*/


        $attr = '';
        $attr .= $this->required ? ' required aria-required="true"' : '';
        $attr .= ' class="inputbox"';


		$required	= ((string) $this->element['required'] == 'true') ? TRUE : FALSE;

		$tree = array();
		$text = '';
		$tree = PhocaPanoramaHelper::CategoryTreeOption($data, $tree, 0, $text, $catId);

		//if ($required == TRUE) {

		//} else {

			array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_PHOCAPANORAMA_SELECT_CATEGORY').' -', 'value', 'text'));
		//}
		return JHTML::_('select.genericlist',  $tree,  $this->name, trim($attr), 'value', 'text', $this->value, $this->id );
	}
}
?>
