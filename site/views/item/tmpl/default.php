<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
echo '<div id="ph-pp-item-box" class="pp-item-view'.$this->t['p']->get( 'pageclass_sfx' ).'">';

if ( $this->t['p']->get( 'show_page_heading' ) ) {
	echo '<h1>'. $this->escape($this->t['p']->get('page_heading')) . '</h1>';
} else {
	echo '<h1>'. $this->escape($this->item[0]->title) . '</h1>';
}

if (isset($this->category[0]->id) && ($this->t['display_back'] == 2 || $this->t['display_back'] == 3)) {
	if ($this->category[0]->id > 0) {
		$linkUp = JRoute::_(PhocaPanoramaRoute::getCategoryRoute($this->category[0]->id, $this->category[0]->alias));
		$linkUpText = $this->category[0]->title;
	} else {
		$linkUp 	= false;
		$linkUpText = false;
	}

	if ($linkUp && $linkUpText) {
		echo '<div class="ph-top">'
		.'<a class="btn btn-success" title="'.$linkUpText.'" href="'. $linkUp.'" ><span class="glyphicon glyphicon-arrow-left"></span> '.JText::_($linkUpText).'</a></div>';
	}
}

if ( isset($this->item[0]->description) && $this->item[0]->description != '') {
	echo '<div class="ph-desc">'. $this->item[0]->description. '</div>';
}

if (!empty($this->item[0])) {

	if (isset($this->item[0]->iframe_link) && $this->item[0]->iframe_link != '' ) {
		echo '<iframe style="width:'.$this->t['panorama_width'].';height:'.$this->t['panorama_height'].';border: 0px" src="'.htmlspecialchars($this->item[0]->iframe_link).'"  allowfullscreen=""></iframe>';
	} else if ($this->t['display_method'] == 1) {
		$tourAbs 	= $this->t['panoramapathabs'] . htmlspecialchars($this->item[0]->folder).'/'.$this->t['file_name'].'.html';
		$tourRel 	= $this->t['panoramapathrel'] . htmlspecialchars($this->item[0]->folder).'/'.$this->t['file_name'].'.html';
		echo '<iframe style="width:'.$this->t['panorama_width'].';height:'.$this->t['panorama_height'].';border: 0px" src="'.$tourRel.'"  allowfullscreen=""></iframe>';
	} else {

		$this->document->setMetadata('viewport', 'target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0');
		$this->document->setMetadata('apple-mobile-web-app-capable', 'yes');

		$s = '@-ms-viewport { width: device-width; }
			@media only screen and (min-device-width: 800px) { html { overflow:hidden; } }
			html { height:100%; }
			body { height:100%; overflow:hidden; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#FFFFFF; background-color:#000000; }';
		//$this->document->addCustomTag('<style type="text/css">'.$s.'</style>');
		$tourAbs 	= $this->t['panoramapathabs'] . htmlspecialchars($this->item[0]->folder).'/'.$this->t['file_name'].'.js';
		$tourRel 	= $this->t['panoramapathrel'] . htmlspecialchars($this->item[0]->folder).'/'.$this->t['file_name'].'.js';
		$path 		= JURI::base(true). '/' . $this->t['panoramapathrel'] . htmlspecialchars($this->item[0]->folder);

		if (JFile::exists($tourAbs)) {
			$this->document->addScript(JURI::root(true).'/'.$tourRel);
			echo '<div id="ph-pano" style="width:'.$this->t['panorama_width'].';height:'.$this->t['panorama_height'].';">';
			echo '<script type="text/javascript">'. "\n";
			echo 'var viewer = createPanoViewer({swf:"'. $path .'/'.$this->t['file_name'].'.swf", target:"ph-pano", html5:"auto", passQueryParameters:true});'. "\n";
			echo 'viewer.addVariable("xml", "'.$path .'/'.$this->t['file_name'].'.xml");'. "\n";
			echo 'viewer.embed();'."\n";
			echo '</script>'. "\n";
			echo '</div>';
		}

	}
}
echo '</div>';
echo '<div>&nbsp;</div>';
echo PhocaPanoramaHelper::getFooter();
?>
