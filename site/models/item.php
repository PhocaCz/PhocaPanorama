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

class PhocaPanoramaModelItem extends JModelLegacy
{
	var $_item 				= null;
	var $_category 			= null;
	var $_itemname			= null;

	function __construct() {
		$app	= JFactory::getApplication();
		parent::__construct();
		
		$this->setState('filter.language',$app->getLanguageFilter());
	}

	function getItem( $itemId) {
		if (empty($this->_item)) {			
			$query			= $this->_getitemQuery( $itemId );
			$this->_item	= $this->_getList( $query, 0 , 1 );

			if (empty($this->_item)) {
				return null;
			} 

		}
		return $this->_item;
	}
	
	function _getItemQuery( $itemId ) {
		
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();

		$categoryId	= 0;
		$category	= $this->getCategory($itemId);
		if (isset($category[0]->id)) {
			$categoryId = $category[0]->id;
		}
		
		$wheres[]	= " c.catid= ".(int) $categoryId;
		$wheres[]	= " c.catid= cc.id";
		$wheres[] = " c.published = 1";
		$wheres[] = " cc.published = 1";
		$wheres[] = " c.id = " . (int) $itemId;
		
		if ($this->getState('filter.language')) {
			$wheres[] =  ' c.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
			$wheres[] =  ' cc.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}
		
		$query = ' SELECT c.*, cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias'
				.' FROM #__phocapanorama_items AS c' 
				.' LEFT JOIN #__phocapanorama_categories AS cc ON cc.id = c.catid'
				.' WHERE ' . implode( ' AND ', $wheres )
				.' ORDER BY c.ordering';		
		return $query;
	}
	
	function getCategory($itemId) {
		if (empty($this->_category)) {			
			$query			= $this->_getCategoryQuery( $itemId );
			$this->_category= $this->_getList( $query, 0, 1 );
		}
		return $this->_category;
	}
	
	function _getCategoryQuery( $itemId ) {
		
		$wheres		= array();
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();

		$wheres[]	= " c.id= ".(int)$itemId;
		$wheres[] = " cc.published = 1";
		
		if ($this->getState('filter.language')) {
			$wheres[] =  ' c.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
			$wheres[] =  ' cc.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}
		
		$query = " SELECT cc.id, cc.title, cc.alias, cc.description"
				. " FROM #__phocapanorama_categories AS cc"
				. " LEFT JOIN #__phocapanorama_items AS c ON c.catid = cc.id"
				. " WHERE " . implode( " AND ", $wheres )
				. " ORDER BY cc.ordering";		
		return $query;
	}
}
?>