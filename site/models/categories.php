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

class PhocaPanoramaModelCategories extends JModelLegacy
{
	var $_categories 			= null;
	var $_most_viewed_docs 		= null;
	var $_categories_ordering	= null;
	var $_category_ordering		= null;

	function __construct() {
		$app	= JFactory::getApplication();
		parent::__construct();
		
		$this->setState('filter.language',$app->getLanguageFilter());
	}

	function getCategoriesList() {
		if (empty($this->_categories)) {				
			$query				= $this->_getCategoriesListQuery();
			$this->_categories 	= $this->_getList( $query );
			
			if (!empty($this->_categories)) {
				foreach ($this->_categories as $key => $value) {
				
					$query	= $this->_getCategoryListQuery( $value->id );
					$this->_categories[$key]->subcategories = $this->_getList( $query );
				}
			}
			
		}
		return $this->_categories;
	}
	
	/* 
	 * Get only parent categories
	 */
	function _getCategoriesListQuery(  ) {
		
		$wheres		= array();
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();
		
		$display_categories = $params->get('display_categories', '');
		$hide_categories 	= $params->get('hide_categoriess', '');
		
		if ( $display_categories != '' ) {
			$wheres[] = " cc.id IN (".$display_categories.")";
		}
		
		if ( $hide_categories != '' ) {
			$wheres[] = " cc.id NOT IN (".$hide_categories.")";
		}
		$wheres[] = " cc.parent_id = 0";
		$wheres[] = " cc.published = 1";
		
		if ($this->getState('filter.language')) {
			$wheres[] =  ' cc.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}
		
		$categoriesOrdering = $this->_getCategoryOrdering();
		
		$query =  " SELECT cc.id, cc.title, cc.alias, cc.image, cc.description, COUNT(c.id) AS numdoc"
				. " FROM #__phocapanorama_categories AS cc"
				. " LEFT JOIN #__phocapanorama_items AS c ON c.catid = cc.id AND c.published = 1"
				. " WHERE " . implode( " AND ", $wheres )
				. " GROUP BY cc.id"
				. " ORDER BY cc.".$categoriesOrdering;
		return $query;
	}
	
	
	/* 
	 * Get only first level under parent categories
	 */
	function _getCategoryListQuery( $parentCatId ) {
		
		$wheres		= array();
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();

		$display_categories = $params->get('display_categories', '');
		$hide_categories 	= $params->get('hide_categoriess', '');
		
		if ( $display_categories != '' ) {
			$wheres[] = " cc.id IN (".$display_categories.")";
		}
		
		if ( $hide_categories != '' ) {
			$wheres[] = " cc.id NOT IN (".$hide_categories.")";
		}
		$wheres[] = " cc.parent_id = ".(int)$parentCatId;
		$wheres[] = " cc.published = 1";
		
		if ($this->getState('filter.language')) {
			$wheres[] =  ' cc.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}
		
		$categoryOrdering = $this->_getCategoryOrdering();

		
		$query = " SELECT  cc.id, cc.title, cc.alias, cc.image, COUNT(c.id) AS numdoc"
				. " FROM #__phocapanorama_categories AS cc"
				. " LEFT JOIN #__phocapanorama_items AS c ON c.catid = cc.id AND c.published = 1"
				. " WHERE " . implode( " AND ", $wheres )
				. " GROUP BY cc.id"
				. " ORDER BY cc.".$categoryOrdering;
		return $query;
	}
	
	function getMostViewedDocsList() {
		
		if (empty($this->_most_viewed_docs)) {			
			$query						= $this->_getMostViewedDocsListQuery();
			$this->_most_viewed_docs 	= $this->_getList( $query );
		}
		return $this->_most_viewed_docs;
	}
	
	function _getMostViewedDocsListQuery() {
		
		$wheres		= array();
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();

		$most_viewed_docs_num 	= $params->get( 'most_viewed_docs_num', 5 );
		$display_categories 	= $params->get('display_categories', '');
		$hide_categories 		= $params->get('hide_categoriess', '');
		
		if ( $display_categories != '' ) {
			$wheres[] = " cc.id IN (".$display_categories.")";
		}
		
		if ( $hide_categories != '' ) {
			$wheres[] = " cc.id NOT IN (".$hide_categories.")";
		}
		

		$wheres[]	= " c.catid= cc.id";
		$wheres[]	= " c.published= 1";
		
		if ($this->getState('filter.language')) {
			$wheres[] =  ' c.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
			$wheres[] =  ' cc.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}

		$query = " SELECT c.id, c.title, c.alias, c.date, c.hits, c.image, cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias "
				." FROM #__phocapanorama_items AS c, #__phocapanorama_categories AS cc"
				. " WHERE " . implode( " AND ", $wheres )
				. " ORDER BY c.hits DESC"
				. " LIMIT ".(int)$most_viewed_docs_num;
		return $query;
	}
	
	function _getCategoryOrdering() {
		if (empty($this->_category_ordering)) {
	
			$app						= JFactory::getApplication();
			$params 					= $app->getParams();
			$ordering					= $params->get( 'category_ordering', 1 );
			$this->_category_ordering 	= PhocaPanoramaOrdering::getOrderingText($ordering);

		}
		return $this->_category_ordering;
	}
}
?>