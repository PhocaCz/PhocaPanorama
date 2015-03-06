<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
echo '<div id="ph-pp-category-box" class="pp-category-view'.$this->t['p']->get( 'pageclass_sfx' ).'">';
if ( $this->t['p']->get( 'show_page_heading' ) ) { 
	echo '<h1>'. $this->escape($this->t['p']->get('page_heading')) . '</h1>';
}


if (isset($this->category[0]->parentid) && ($this->t['display_back'] == 1 || $this->t['display_back'] == 3)) {
	if ($this->category[0]->parentid == 0) {
		$linkUp = JRoute::_(PhocaPanoramaRoute::getCategoriesRoute());
		$linkUpText = JText::_('COM_PHOCAPANORAMA_CATEGORIES');
	} else if ($this->category[0]->parentid > 0) {
		$linkUp = JRoute::_(PhocaPanoramaRoute::getCategoryRoute($this->category[0]->parentid, $this->category[0]->parentalias));
		$linkUpText = $this->category[0]->parenttitle;
	} else {
		$linkUp 	= false;
		$linkUpText = false; 
	}
	
	if ($linkUp && $linkUpText) {
		echo '<div class="ph-top">'
		.'<a class="btn btn-success" title="'.$linkUpText.'" href="'. $linkUp.'" ><span class="glyphicon glyphicon-arrow-left"></span> '.JText::_($linkUpText).'</a></div>';
	}
}

if ( isset($this->category[0]->description) && $this->category[0]->description != '') {
	echo '<div class="ph-desc">'. $this->category[0]->description. '</div>';
}


if (!empty($this->subcategories) && (int)$this->t['display_subcat_cat_view'] > 0) {
	echo '<div class="ph-subcategories">'.JText::_('COM_PHOCAPANORAMA_SUBCATEGORIES') . ':</div>';
	echo '<ul>';
	$j = 0;
	foreach($this->subcategories as $v) {
		if ($j == (int)$this->t['display_subcat_cat_view']) {
			break;
		}
		echo '<li><a href="'.PhocaPanoramaRoute::getCategoryRoute($v->id, $v->alias).'">'.$v->title.'</a></li>';
		$j++;
	}
	echo '</ul>';
	echo '<hr />';
}



if (!empty($this->items)) {
	echo '<div class="ph-items">';
	$i = 0;
	$c = count($this->items);
	foreach ($this->items as $v) {
		
		if ($i%3==0) { echo '<div class="row">';}
		
		echo '<div class="col-sm-6 col-md-4">';
		echo '<div class="thumbnail ph-thumbnail">';
		
		$imageAbs = $this->t['panoramapathabs'] . htmlspecialchars($v->folder).'/thumb.jpg';
		$imageRel = $this->t['panoramapathrel'] . htmlspecialchars($v->folder).'/thumb.jpg';
		if (isset($v->image) && $v->image != '') {
			echo '<img src="'. JURI::base(true) . '/' . $v->image.'" alt="" style="width:'.$this->t['image_width'].'px;height:'.$this->t['image_height'].'px" >';
		} else if (JFile::exists($imageAbs)) {
			echo '<img src="'.$imageRel.'" alt="" style="width:'.$this->t['image_width'].'px;height:'.$this->t['image_height'].'px" >';
		}
		echo '<div class="caption">';
		echo '<h3>'.$v->title.'</h3>';
		
		// Description box will be displayed even no description is set - to set height and have all columns same height
		echo '<div class="ph-item-desc">';
		if ($v->description != '') {
			echo $v->description;
		}
		echo '</div>';
		
		echo '<p class="pull-right"><a href="'.PhocaPanoramaRoute::getItemRoute($v->id, $v->catid, $v->alias, $v->categoryalias).'" class="btn btn-primary" role="button">'.JText::_('COM_PHOCAPANORAMA_VIEW_PANORAMA').'</a></p>';
		echo '<div class="clearfix"></div>';
		echo '</div>';
		
		echo '</div>';
		echo '</div>';
		
		$i++; if ($i%3==0 || $c==$i) { echo '</div>';}
		
	}
	echo '</div>';
	
	
	echo $this->loadTemplate('pagination');
}
echo '</div>';
echo '<div>&nbsp;</div>';
echo PhocaPanoramaHelper::getFooter();
?>