<?php
/**
 * @version		$Id: route.php 11190 2008-10-20 00:49:55Z ian $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.helper');

class PhocaPanoramaRoute
{
	public static function getItemRoute($id, $catid = 0, $idAlias = '', $catidAlias = '')
	{
		$needles = array(
			'item'  => (int) $id,
			'category' => (int) $catid,
			'categories' => ''
		);


		if ($idAlias != '') {
			$id = $id . ':' . $idAlias;
		}
		if ($catidAlias != '') {
			$catid = $catid . ':' . $catidAlias;
		}

		$link = 'index.php?option=com_phocapanorama&view=item&id='. $id;


		if($item = self::_findItem($needles)) {
			if (isset($item->id)) {
				$link .= '&Itemid='.$item->id;
			}
		}

		return $link;
	}


	public static function getCategoryRoute($catid, $catidAlias = '')
	{
		// TEST SOLUTION
		$app 		= JFactory::getApplication();
		$menu 		= $app->getMenu();
		$active 	= $menu->getActive();
		$activeId 	= $active->id;
		if ((int)$activeId > 0) {
			$needles 	= array(
				'category' => (int)$catid,
				'categories' => (int)$activeId
			);
		} else {
			$needles = array(
				'category' => (int)$catid,
				'categories' => ''
			);
		}

		if ($catidAlias != '') {
			$catid = $catid . ':' . $catidAlias;
		}

		//Create the link
		$link = 'index.php?option=com_phocapanorama&view=category&id='.$catid;

		if($item = self::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			if(isset($item->id)) {
				$link .= '&Itemid='.$item->id;
			}
		};

		return $link;
	}


	public static function getCategoriesRoute()
	{
		// TEST SOLUTION
		$app 		= JFactory::getApplication();
		$menu 		= $app->getMenu();
		$active 	= $menu->getActive();
		$activeId 	= $active->id;

		$itemId 	= 0;
		// 1) get standard item id if exists
		if (isset($item->id)) {
			$itemId = (int)$item->id;
		}

		$option			= $app->input->get( 'option', '', 'string' );
		$view			= $app->input->get( 'view', '', 'string' );
		if ($option == 'com_phocapanorama' && $view == 'category') {
			if ((int)$activeId > 0) {
				// 2) if there are two menu links, try to select the one active
				$itemId = $activeId;
			}
		}

		$needles = array(
			'categories' => ''
		);

		//Create the link
		$link = 'index.php?option=com_phocapanorama&view=categories';

		if($item = self::_findItem($needles, 1)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}

			// TEST SOLUTION
			if ((int)$itemId > 0) {
				$link .= '&Itemid='.(int)$itemId;
			}

			/*if (isset($item->id)) {
				$link .= '&Itemid='.$item->id;
			}*/
		};

		return $link;
	}



	protected static function _findItem($needles, $notCheckId = 0)
	{
		$app = JFactory::getApplication();
		$menus	= $app->getMenu('site', array());
		$items	= $menus->getItems('component', 'com_phocapanorama');

		if(!$items) {
			return $app->input->get('Itemid', 0, '', 'int');
			//return null;
		}

		$match = null;


		foreach($needles as $needle => $id)
		{

			if ($notCheckId == 0) {
				foreach($items as $item) {
					if ((@$item->query['view'] == $needle) && (@$item->query['id'] == $id)) {
						$match = $item;
						break;
					}
				}
			} else {
				foreach($items as $item) {
					if (@$item->query['view'] == $needle) {
						$match = $item;
						break;
					}
				}
			}

			if(isset($match)) {
				break;
			}
		}

		return $match;
	}
}
?>
