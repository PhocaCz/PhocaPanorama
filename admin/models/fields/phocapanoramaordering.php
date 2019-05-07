<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_BASE') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldPhocaPanoramaOrdering extends JFormField
{

	protected $type = 'PhocaPanoramaOrdering';

	protected function getInput() {
		// Initialize variables.
		$html = array();
		$attr = '';

		// Get some field values from the form.
		$id			= (int) $this->form->getValue('id');
		
		if ($this->element['table']) {
			switch (strtolower($this->element['table'])) {
			
				case "category":
					$whereLabel	=	'parent_id';
					$whereValue	=	(int) $this->form->getValue('parent_id');
					$table		=	'#__phocapanorama_categories';
				break;
				
			
				
				case "item":
				default:
					$whereLabel	=	'catid';
					$whereValue	=	(int) $this->form->getValue('catid');
					$table		=	'#__phocapanorama_items';
				break;
				
				
			}
		} else {
			$whereLabel	=	'catid';
			$whereValue	=	(int) $this->form->getValue('catid');
			$table		=	'#__phocapanorama_ordering';
		}
		
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		

		// Build the query for the ordering list.
		$query = 'SELECT ordering AS value, title AS text' .
				' FROM ' . $table;
		if ($whereLabel != '') {
			$query .= ' WHERE '.$whereLabel.' = ' . (int) $whereValue;
		}
		$query .= ' ORDER BY ordering';

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('list.ordering', '', $query, trim($attr), $this->value, $id ? 0 : 1);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('list.ordering', $this->name, $query, trim($attr), $this->value, $id ? 0 : 1);
		}

		return implode($html);
	}
}