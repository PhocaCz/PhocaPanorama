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

class PhocaPanoramaModelCategory extends JModelLegacy
{
	var $_document 			= null;
	var $_category 			= null;
	var $_subcategories 	= null;
	var $_item				= null;
	var $_item_ordering		= null;
	var $_category_ordering	= null;
	var $_pagination		= null;
	var $_total				= null;

	function __construct() {

		$app	= JFactory::getApplication();

		parent::__construct();

		$config = JFactory::getConfig();

		$paramsC 			= JComponentHelper::getParams('com_phocapanorama') ;
		$defaultPagination	= $paramsC->get( 'default_pagination', '20' );

		// Get the pagination request variables
		$this->setState('limit', $app->getUserStateFromRequest('com_phocapanorama.limit', 'limit', $defaultPagination, 'int'));
		$this->setState('limitstart', $app->input->get('limitstart', 0, 'int'));

		// In case limit has been changed, adjust limitstart accordingly
		$this->setState('limitstart', ($this->getState('limit') != 0 ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit')) : 0));

		$this->setState('filter.language',$app->getLanguageFilter());

		// Get the filter request variables
		$this->setState('filter_order', JFactory::getApplication()->input->getCmd('filter_order', 'ordering'));
		$this->setState('filter_order_dir', JFactory::getApplication()->input->getCmd('filter_order_Dir', 'ASC'));

	}

	function getPagination($categoryId) {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new PhocaPanoramaPagination( $this->getTotal($categoryId), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function getTotal($categoryId) {
		if (empty($this->_total)) {
			$query = $this->_getItemListQuery($categoryId);
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}

	function getItemList($categoryId) {
		if (empty($this->_document)) {
			$query			= $this->_getItemListQuery( $categoryId);
			$this->_document= $this->_getList( $query ,$this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_document;
	}

	function getCategory($categoryId) {
		if (empty($this->_category)) {
			$query					= $this->_getCategoriesQuery( $categoryId, FALSE );
			$this->_category 		= $this->_getList( $query, 0, 1 );
		}
		return $this->_category;
	}

	function getSubcategories($categoryId) {
		if (empty($this->_subcategories)) {
			$query					= $this->_getCategoriesQuery( $categoryId, TRUE );
			$this->_subcategories 	= $this->_getList( $query );
		}
		return $this->_subcategories;
	}

	function _getItemListQuery( $categoryId) {

		$wheres		= array();
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();

		if ((int)$categoryId > 0) {
			$wheres[]			= " cc.id = ".(int)$categoryId;
		}
		$wheres[] = ' c.published = 1';
		$wheres[] = ' cc.published = 1';

		if ($this->getState('filter.language')) {
			$wheres[] =  ' c.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
			$wheres[] =  ' cc.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}

		$fileOrdering = $this->_getItemOrdering();

		$query = ' SELECT c.*, cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias'
				.' FROM #__phocapanorama_items AS c'
				.' LEFT JOIN #__phocapanorama_categories AS cc ON cc.id = c.catid'
				. ' WHERE ' . implode( ' AND ', $wheres )
				. ' ORDER BY c.'.$fileOrdering;
		return $query;
	}

	function _getCategoriesQuery( $categoryId, $subcategories = FALSE ) {

		$wheres		= array();
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();

		// Get the current category or get parent categories of the current category
		if ($subcategories) {
			$wheres[]			= " cc.parent_id = ".(int)$categoryId;
			$categoryOrdering 	= $this->_getCategoryOrdering();
		} else {
			$wheres[]	= " cc.id= ".(int)$categoryId;
		}

		$wheres[] = " cc.published = 1";

		if ($this->getState('filter.language')) {
			$wheres[] =  ' cc.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}

		if ($subcategories) {
			$query = " SELECT  cc.id, cc.title, cc.alias, COUNT(c.id) AS numdoc"
				. " FROM #__phocapanorama_categories AS cc"
				. " LEFT JOIN #__phocapanorama_items AS c ON c.catid = cc.id AND c.published = 1"
				. " WHERE " . implode( " AND ", $wheres )
				. " GROUP BY cc.id"
				. " ORDER BY cc.".$categoryOrdering;
		} else {
			$query = " SELECT cc.id, cc.title, cc.alias, cc.description, cc.metakey, cc.metadesc, pc.title as parenttitle, cc.parent_id as parentid, pc.alias as parentalias"
				. " FROM #__phocapanorama_categories AS cc"
				. " LEFT JOIN #__phocapanorama_categories AS pc ON pc.id = cc.parent_id"
				. " WHERE " . implode( " AND ", $wheres )
				. " ORDER BY cc.ordering";
		}

		return $query;
	}


	function _getItemOrdering() {
		if (empty($this->_item_ordering)) {
			$app						= JFactory::getApplication();
			$params						= $app->getParams();
			$ordering					= $params->get( 'item_ordering', 1 );
			$this->_item_ordering 		= PhocaPanoramaOrdering::getOrderingText($ordering);

		}
		return $this->_item_ordering;
	}

	function _getCategoryOrdering() {
		if (empty($this->_category_ordering)) {

			$app						= JFactory::getApplication();
			$params						= $app->getParams();
			$ordering					= $params->get( 'category_ordering', 1 );
			$this->_category_ordering 	= PhocaPanoramaOrdering::getOrderingText($ordering);

		}
		return $this->_category_ordering;
	}
}
?>
