<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

/**
 * Method to build Route
 * @param array $query
 */ 
function PhocaPanoramaBuildRoute(&$query)
{
	
	static $items;
	$segments	= array();
	$itemid		= null;
	

	// Break up the weblink/category id into numeric and alias values.
	if (isset($query['id']) && strpos($query['id'], ':')) {
		list($query['id'], $query['alias']) = explode(':', $query['id'], 2);
	}

	// Break up the category id into numeric and alias values.
/*	if (isset($query['catid']) && strpos($query['catid'], ':')) {
		list($query['catid'], $query['catalias']) = explode(':', $query['catid'], 2);
	}*/

	// Get the menu items for this component.
	if (!$items) {

		$app		= JFactory::getApplication();
		$menu		= $app->getMenu();
		$items		= $menu->getItems('component', 'com_phocapanorama');
	
	}

	// Search for an appropriate menu item.
	if (is_array($items))
	{
		// If only the option and itemid are specified in the query, return that item.
		if (!isset($query['view']) && !isset($query['id']) && !isset($query['catid']) && !isset($query['download']) && isset($query['Itemid'])) {
			$itemid = (int) $query['Itemid'];
		}

	
		// ------------------------------------------------------
		// Search for a specific link based on the critera given.
		if (!$itemid)
		{
			foreach ($items as $item)
			{
				// Check if this menu item links to this view.
				if (isset($item->query['view']) && $item->query['view'] == 'category'
					
					&& isset($query['view']) && $query['view'] != 'item'
					
					&& isset($item->query['id']) && isset($query['id']) && $item->query['id'] == $query['id']) {
						$itemid	= $item->id;
				}
				
				else if (isset($item->query['view']) && $item->query['view'] == 'item'
					&& isset($query['view']) && $query['view'] != 'category'
					
					
					&& isset($item->query['id']) && isset($query['id']) && $item->query['id'] == $query['id']) {
						$itemid	= $item->id;
				}
			}
			
		}
	}

	// Check if the router found an appropriate itemid.
	if (!$itemid) {
		// Check if a category was specified
		if (isset($query['id'])) { // Check if a id was specified.
			if (isset($query['alias'])) {
				$query['id'] .= ':'.$query['alias'];
			}

			// Push the id onto the stack.
			//$segments[] = $query['id'];
			if(isset($query['view'])) {$segments[]	= $query['view'];}
			$segments[] = $query['id'];
			unset($query['view']);
			unset($query['id']);
			unset($query['alias']);
			unset($query['catid']);
			unset($query['catalias']);
			
		} else {
			// Categories view.
			unset($query['view']);
		}
	} else {
		$query['Itemid'] = $itemid;
		// Remove the unnecessary URL segments.
		unset($query['view']);
		unset($query['id']);
		unset($query['alias']);
	}
	
	return $segments;
}

/**
 * Method to parse Route
 * @param array $segments
 */ 
function PhocaPanoramaParseRoute($segments)
{
	$vars = array();

	//Get the active menu item
	$app		= JFactory::getApplication();
	$menu		= $app->getMenu();
	$item 		= $menu->getActive();


	// Count route segments
	$count = count($segments);

	//Standard routing
	if(!isset($item))  {
		if($count == 3 ) {
			$vars['view']  = $segments[$count - 3];
		} else if ($count == 2) {
			$vars['view']  = $segments[$count - 2];
		} else {
			$vars['view'] = 'category';
		}
		$vars['id']    = $segments[$count - 1];
		
	} else {
		//Handle View and Identifier

		switch($item->query['view'])
		{
			case 'categories' :
				if($count == 1) {
					$vars['view'] 	= 'categories';
					$vars['id'] 	= $segments[$count-1];
				}

				if($count == 2) {
					$vars['view'] 	= $segments[$count-2];
					$vars['id'] 	= $segments[$count-1];
				}				
			break;
			
		
			
			case 'category'   :
				if($count == 1) {
					$vars['view'] 	= 'category';
				}

				if($count == 2) {
					$vars['view'] 	= $segments[$count-2];
					$vars['id'] 	= $segments[$count-1];
				}
			break;
			
			case 'item'   :
				if($count == 1) {
					$vars['view'] 	= 'item';
				}

				if($count == 2) {
					$vars['view'] 	= $segments[$count-2];
					$vars['id'] 	= $segments[$count-1];
				}
				
			break;
		}
	}
	return $vars;
}
?>