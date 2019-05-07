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

        JPluginHelper::importPlugin('phocatools');
        $results = \JFactory::getApplication()->triggerEvent('PhocatoolsOnDisplayInfo', array('NjI5NTcxMTc3MTE3'));
        if (isset($results[0]) && $results[0] === true) {
            return '';
        }
        return '<div style="text-align:right;color:#ccc;display:block">Powered by <a href="https://www.phoca.cz/phocapanorama" target="_blank" title="Phoca Panorama">Phoca Panorama</a></div>';

    }



    public static function filterValue($string, $type = 'html') {

        switch ($type) {

            case 'url':
                return rawurlencode($string);
            break;

            case 'number':
                return preg_replace( '/[^.0-9]/', '', $string );
            break;

            case 'alphanumeric':
                return preg_replace("/[^a-zA-Z0-9]+/", '', $string);
            break;

            case 'alphanumeric2':
                return preg_replace("/[^\\w-]/", '', $string);// Alphanumeric plus _  -
            break;

            case 'alphanumeric3':
                return preg_replace("/[^\\w.-]/", '', $string);// Alphanumeric plus _ . -
            break;

            case 'folder':
            case 'file':
                $string =  preg_replace('/[\"\*\/\\\:\<\>\?\'\|]+/', '', $string);
                return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            break;

            case 'folderpath':
            case 'filepath':
                $string = preg_replace('/[\"\*\:\<\>\?\'\|]+/', '', $string);
                return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            break;

            case 'text':
                return htmlspecialchars(strip_tags($string), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            break;

            case 'html':
            default:
                return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            break;

        }

    }
}
?>
