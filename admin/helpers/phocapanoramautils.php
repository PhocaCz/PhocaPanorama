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

class PhocaPanoramaUtils
{
	public static function getAliasName($alias) {
		$alias = JApplicationHelper::stringURLSafe($alias);
		if (trim(str_replace('-', '', $alias)) == '') {
			$alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}
		return $alias;
	}

	public static function setVars( $task = '') {

		$a			= array();
		$app		= JFactory::getApplication();
		$a['o'] 	= htmlspecialchars(strip_tags($app->input->get('option')));
		$a['c'] 	= str_replace('com_', '', $a['o']);
		$a['n'] 	= 'Phoca' . ucfirst(str_replace('com_phoca', '', $a['o']));
		$a['l'] 	= strtoupper($a['o']);
		$a['i']		= 'media/'.$a['o'].'/images/administrator/';
		$a['s']		= 'media/'.$a['o'].'/css/administrator/'.$a['c'].'.css';
		$a['task']	= $a['c'] . htmlspecialchars(strip_tags($task));
		$a['tasks'] = $a['task']. 's';
		return $a;
	}

	public static function getExtensionVersion($c = 'phocapanorama') {
		$folder = JPATH_ADMINISTRATOR . '/components/com_'.$c;
		if (JFolder::exists($folder)) {
			$xmlFilesInDir = JFolder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE . 'components/com_'.$c;
			if (JFolder::exists($folder)) {
				$xmlFilesInDir = JFolder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = array();
		if (!empty($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = \JInstaller::parseXMLInstallFile($folder.'/'.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}

		if (isset($xml_items['version']) && $xml_items['version'] != '' ) {
			return $xml_items['version'];
		} else {
			return '';
		}
	}

	public static function getPath(){


		$paramsC			= JComponentHelper::getParams( 'com_phocapanorama' );
		$panorama_folder		= $paramsC->get( 'panorama_folder', '' );

		$path = array();

		$folder = 'phocapanorama';
		if ($panorama_folder	!= '') {
			$folder = htmlspecialchars(strip_tags($panorama_folder));
		}

		$path['rel']			= $folder.'/';//JURI::root() . 'phocapanorama/';
		$path['abs']			= JPATH_ROOT .'/'.$folder.'/';

		return $path;
	}
}
?>
