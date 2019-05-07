<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

$classSuffix = '';
if ($this->t['equal_height'] == 1) {
    $classSuffix = ' equalHeight';
}

echo '<div id="ph-pp-categories-box" class="pp-categories-view'.$this->t['p']->get( 'pageclass_sfx' ).$classSuffix.'">';
if ( $this->t['p']->get( 'show_page_heading' ) ) {
	echo '<h1>'. $this->escape($this->t['p']->get('page_heading')) . '</h1>';
}
if ( $this->t['description'] != '') {
	echo '<div class="ph-desc">'. $this->t['description']. '</div>';
}
if (!empty($this->t['categories'])) {
	echo '<div class="ph-categories">';
	$i = 0;
	$c = count($this->t['categories']);
    echo '<div class="row ph-pp-row">';
	foreach ($this->t['categories'] as $v) {

		//if ($i%3==0) {
		 //   echo '<div class="row ph-pp-row">';
		//}

		echo '<div class="col-sm-6 col-md-4 ph-pp-col">';
		echo '<div class="thumbnail ph-thumbnail ph-pp-thumbnail">';
		if ($v->image != '') {
			echo '<img src="'.JURI::base(true).'/'.$v->image.'" alt="" style="width:'.$this->t['image_width'].';height:'.$this->t['image_height'].'" class="img-responsive">';
		}
		echo '<div class="caption">';
		echo '<h3>'.$v->title.'</h3>';

		if (!empty($v->subcategories) && (int)$this->t['display_subcat_cats_view'] > 0) {
			echo '<ul>';
			$j = 0;
			foreach($v->subcategories as $v2) {
				if ($j == (int)$this->t['display_subcat_cats_view']) {
					break;
				}
				echo '<li><a href="'.JRoute::_(PhocaPanoramaRoute::getCategoryRoute($v2->id, $v2->alias)).'">'.$v2->title.'</a></li>';
				$j++;
			}
			echo '</ul>';
		}

		// Description box will be displayed even no description is set - to set height and have all columns same height
		echo '<div class="ph-cat-desc">';
		if ($v->description != '') {
			echo $v->description;
		}
		echo '</div>';

		echo '<p class="pull-right"><a href="'.JRoute::_(PhocaPanoramaRoute::getCategoryRoute($v->id, $v->alias)).'" class="btn btn-primary" role="button">'.JText::_('COM_PHOCAPANORAMA_VIEW_CATEGORY').'</a></p>';
		echo '<div class="clearfix"></div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';

		$i++; //if ($i%3==0 || $c==$i) { echo '</div>';}
	}
	echo '</div>';
    echo '</div>';
}
echo '</div>';
echo '<div>&nbsp;</div>';
echo PhocaPanoramaHelper::getFooter();
?>
