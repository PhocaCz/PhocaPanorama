<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class PhocaPanoramaHelper
{
	public static function CategoryTreeOption($data, $tree, $id=0, $text='', $currentId) {		

		foreach ($data as $key) {	
			$show_text =  $text . $key->text;
			
			if ($key->parentid == $id && $currentId != $id && $currentId != $key->value) {
				$tree[$key->value] 			= new JObject();
				$tree[$key->value]->text 	= $show_text;
				$tree[$key->value]->value 	= $key->value;
				$tree = PhocaPanoramaHelper::CategoryTreeOption($data, $tree, $key->value, $show_text . " - ", $currentId );	
			}	
		}
		return($tree);
	}
	
	public static function categoryOptions($type = 0)
	{

		
		$db = JFactory::getDBO();

       //build the list of categories
		$query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
		. ' FROM #__phocapanorama_categories AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$items = $db->loadObjectList();
	
		$catId	= -1;
		
		$javascript 	= 'class="inputbox" size="1" onchange="submitform( );"';
		
		$tree = array();
		$text = '';
		$tree = PhocaPanoramaHelper::CategoryTreeOption($items, $tree, 0, $text, $catId);
		
		return $tree;

	}
	
	public static function getFooter() {
		echo '<div style="text-align: right;margin:10px auto;">Powered by <a href="http://www.phoca.cz/phocapanorama" target="_blank" title="Phoca Panorama">Phoca Panorama</a></div>';
	}
}
?>