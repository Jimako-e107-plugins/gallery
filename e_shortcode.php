<?php
/*
* Copyright (c) e107 Inc e107.org, Licensed under GNU GPL (http://www.gnu.org/licenses/gpl.txt)
* $Id: e_shortcode.php 12438 2011-12-05 15:12:56Z secretr $
*
* Featurebox shortcode batch class - shortcodes available site-wide. ie. equivalent to multiple .sc files.
*/

if(!defined('e107_INIT'))
{
	exit;
}

// [PLUGINS]/gallery/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('gallery', false, true);


class gallery_shortcodes extends e_shortcode
{

	public  $total        = 0;
	public  $amount       = 3;
	public  $from         = 0;
	public  $page         = 1;       
	public  $curCat       = null;
	public  $sliderCat    = 1;
	public  $slideMode    = false;
	public  $slideCount   = 1;
	private $attFull      = null;
	private $plugPref     = null;
	

	function init()
	{
		$this->plugPref = e107::pref('gallery');

		$imagesPref = $this->plugPref['images'] ?? [];

		$pop_w = vartrue($imagesPref['width'], 1024);
		$pop_h = vartrue($imagesPref['height'], 768);
		$pop_c = vartrue($imagesPref['crop'], 0);

		$this->attFull = [
			'w'    => $pop_w,
			'h'    => $pop_h,
			'x'    => 1,
			'crop' => $pop_c,
		];
	}

	function breadcrumb()
	{
		$breadcrumb = array();

		$template = e107::getTemplate('gallery', 'gallery', 'cat');
 
		if(vartrue($this->curCat))
		{
			$main_caption = $this->sc_main_gallery_caption(array('force'=>true));

			$caption = $this->sc_gallery_caption(array('force'=>false));
			$breadcrumb[] = array('text' => $main_caption, 'url' => e107::url('gallery', 'index', $this->var));
 
			$breadcrumb[] = array('text' => $this->sc_gallery_cat_title('title'), 'url' => e107::url('gallery', 'gallery', $this->curCat));
		}
		else {

			$main_caption = $this->sc_main_gallery_caption(array('force'=>true));
			$breadcrumb[] = array('text' => $main_caption, 'url' => e107::url('gallery', 'index', $this->var));
		}
 
		e107::breadcrumb($breadcrumb);
	}

	/*{MAIN_GALLERY_CAPTION} */
	function sc_main_gallery_caption($parm = '') 
	{
		$caption = '';
		$display_title = $this->plugPref["display_title"];
		if($parm['force']) $display_title = $parm['force'];
	 
		if($display_title) 	$caption = $this->plugPref['page_title'] ?: LAN_PLUGIN_GALLERY_TITLE;
		return $caption ;
	} 

	/*{GALLERY_CAPTION} */
	function sc_gallery_caption($parm = '') 
	{
		$display_title = $this->plugPref["display_title"];
		if($parm['force']) $display_title = $parm['force'];
 
		if(vartrue($this->curCat)) {

			if($display_title) {
				$caption = $this->plugPref['page_title'] ?: LAN_PLUGIN_GALLERY_TITLE;
				$caption .= ": ";
			}
			$caption .= $this->sc_gallery_cat_title('title');

		} 
		else {
			$caption = $this->plugPref['page_title'] ?: LAN_PLUGIN_GALLERY_TITLE;
		}
		return $caption ;
	} 

	function sc_gallery_image_caption($parm = '')
	{
		$tp = e107::getParser();

		if($parm === 'text')
		{
			return $tp->toAttribute($this->var['image_caption']);
		}

		e107_require_once(e_PLUGIN . 'gallery/includes/gallery_load.php');
		// Load prettyPhoto settings and files.
		gallery_load_prettyphoto();

		$plugPrefs = $this->plugPref;
		$hook = varset($plugPrefs['pp_hook'], 'data-gal');

		$text = "<a class='gallery-caption' title='" . $tp->toAttribute($this->var['image_caption']) . "' href='" . $tp->thumbUrl($this->var['image_url'], $this->attFull) . "' " . $hook . "='prettyPhoto[slide]' >";     // Erase  rel"lightbox.Gallery2"  - Write "prettyPhoto[slide]"
		$text .= $this->var['image_caption'];
		$text .= "</a>";
		return $text;
	}

	function sc_gallery_description($parm = '')
	{
		$tp = e107::getParser();
		return $tp->toHTML($this->var['image_description'], true, 'BODY');
	}

	function sc_gallery_breadcrumb($parm = '')
	{
	 	$this->breadcrumb();
 
		$breadcrumb = e107::breadcrumb();
		
		$force = varset($this->plugPref['display_breadcrumbs'], false);
		return e107::getForm()->breadcrumb($breadcrumb, $force );
	}



	/**
	 * All possible parameters
	 * {GALLERY_THUMB=w=200&h=200&thumburl&thumbsrc&imageurl&orig}
	 * w and h - optional width and height of the thumbnail
	 * thumburl - return only the URL of the destination image (large one)
	 * thumbsrc - url to the thumb, as it's written in the src attribute of the image
	 * imageurl - full path to the destination image (no proxy)
	 * actualPreview - large preview will use the original path to the image (no proxy)
	 */
	function sc_gallery_image_thumb($parm = '')
	{
		e107_require_once(e_PLUGIN . 'gallery/includes/gallery_load.php');
		// Load prettyPhoto settings and files.
		gallery_load_prettyphoto();
		
		$plugPrefs = $this->plugPref;
		$hook = varset($plugPrefs['pp_hook'], 'data-gal');

		$tp = e107::getParser();
		$parms = eHelper::scParams($parm);

		$w = vartrue($parms['w']) ? $parms['w'] : $tp->thumbWidth(); // 190; // 160;
		$h = vartrue($parms['h']) ? $parms['h'] : $tp->thumbHeight(); // 130;

		$class = ($this->slideMode == true) ? 'gallery-slideshow-thumb img-responsive img-fluid img-rounded rounded' : varset($parms['class'], 'gallery-thumb img-responsive img-fluid');
		$rel = ($this->slideMode == true) ? 'prettyPhoto[pp_gal]' : 'prettyPhoto[pp_gal]';

		//$att        = array('aw'=>$w, 'ah'=>$h, 'x'=>1, 'crop'=>1);
		$caption = $tp->toAttribute($this->var['image_caption']);
		$att = array('w' => $w, 'h' => $h, 'class' => $class, 'alt' => $caption, 'x' => 1, 'crop' => 1);


		$srcFull = $tp->thumbUrl($this->var['image_url'], $this->attFull);

		if(vartrue($parms['actualPreview']))
		{
			$srcFull = $tp->replaceConstants($this->var['image_url'], 'full');
		}

		if(isset($parms['thumburl']))
		{
			return $srcFull;
		}
		elseif(isset($parms['thumbsrc']))
		{
			return $tp->thumbUrl($this->var['image_url'], $att);
		}
		elseif(isset($parms['imageurl']))
		{
			return $tp->replaceConstants($this->var['image_url'], 'full');
		}

		$description = $tp->toAttribute($this->var['image_description']);

		$text = "<a class='" . $class . "' title='" . $description . "' href='" . $srcFull . "' " . $hook . "='" . $rel . "'>";
		$text .= $tp->toImage($this->var['image_url'], $att);
		$text .= "</a>";

		return $text;
	}

	function sc_gallery_cat_title($parm = '')
	{
		$tp = e107::getParser();
 
		if($parm == 'title')
		{
			return $tp->toHTML($this->var['gallery_title'], false, 'TITLE');
		}

		$url = e107::url('gallery', 'gallery', $this->var);
		$text = "<a href='" . $url . "'>";
		$text .= $tp->toHTML($this->var['gallery_title'], false, 'TITLE');
		$text .= "</a>";
		return $text;
	}

	function sc_gallery_cat_url($parm = '')
	{
		return e107::url('gallery', 'gallery', $this->var);
	}

		function sc_gallery_cat_summary($parm = '')
	{
		$tp = e107::getParser();
		return $tp->toHTML($this->var['gallery_summary'], true, 'BODY');
	}

	function sc_gallery_cat_description($parm = '')
	{
		$tp = e107::getParser();
		return $tp->toHTML($this->var['gallery_description'], true, 'BODY');
	}

	function sc_gallery_baseurl()
	{
		return e107::url('gallery', 'index');
	}

	function sc_gallery_cat_thumb($parm = null)
	{
		$parms = eHelper::scParams($parm);
		$w = !empty($parms['w']) ? $parms['w'] : 500; // 260;
		$h = !empty($parms['h']) ? $parms['h'] : 500; // 180;
		$att = 'aw=' . $w . '&ah=' . $h . '&x=1'; // 'aw=190&ah=150';
		$class = isset($parms['class']) ? $parms['class'] : 'img-responsive img-fluid';

		$url = e107::url('gallery', 'gallery', $this->var);

		if(isset($parms['thumbsrc']))
		{
			return e107::getParser()->thumbUrl($this->var['gallery_image'], $att);
		}

		$text = "<a class='thumbnail' href='" . $url . "'>";
		$text .= "<img class='".$class."' data-src='holder.js/" . $w . "x" . $h . "' src='" . e107::getParser()->thumbUrl($this->var['gallery_image'], $att) . "' alt='' />";
		$text .= "</a>";
		return $text;
	}

 
	function sc_gallery_nextprev()
	{
		$url = e_REQUEST_SELF . '?page=[FROM]';
		$totalPages = $this->total > 0 ? ceil($this->total / $this->amount) : 1;

		return e107::getForm()->pagination(
			$url,
			$totalPages,      // total PAGES
			$this->page,      // current PAGE (1-based)
			$this->amount,
			array('type' => 'page')
		);
	}

	function sc_gallery_slideshow($parm = '')
	{
		$slideCat = $this->plugPref['slideshow_category'];
		$this->sliderCat = ($parm) ? $parm : vartrue($slideCat, 1);

		$tmpl = e107::getTemplate('gallery', 'gallery');
		$template = array_change_key_case($tmpl);
	
 
		return e107::getParser()->parseTemplate($template['slideshow_wrapper']);
	}

	/**
	 * Display a Grid of thumbnails - useful for home pages.
	 * Amount per row differs according to device, so they are not set here, only the amount.
	 * @example {GALLERY_PORTFOLIO: placeholder=1&category=2}
	 */
	function sc_gallery_portfolio($parm=null)
	{
		$ns = e107::getRender();
		$tp = e107::getParser();
	//	$parm = eHelper::scParams($parms);
		$slideCat = $this->plugPref['slideshow_category'];
		$cat = (!empty($parm['category'])) ? $parm['category'] : vartrue($slideCat, false); //TODO Separate pref?

		$tmpl = e107::getTemplate('gallery', 'gallery');
		$limit = vartrue($parm['limit'], 6);

		$plugPrefs = $this->plugPref;
		$orderBy = varset($plugPrefs['orderby'], 'image_id DESC');

		$imageQry = (empty($cat) || $cat==1) ? "gallery_image|gallery_image_1|gallery_1" : 'gallery_' . $cat . '|gallery_image_' . $cat;


		$list =  e107::getDb()->retrieve('gallery_images', "*", "`gallery_image_active`=1", true);
		//$list = e107::getMedia()->getImages($imageQry, 0, $limit, null, $orderBy);

		if(count($list) < 1 && vartrue($parm['placeholder']))
		{
			$list = array();

			for($i = 0; $i < $limit; $i++)
			{
				$list[] = array('image_url' => '');
			}
		}

		$template = e107::getTemplate('gallery', 'gallery', 'portfolio');
 
		if(!empty($template['start']))
		{
			$text = $tp->parseTemplate($template['start'],true, $this);
		}
		else
		{
			$text = '';
		}

		//NOTE: Using tablerender() allows the theme developer to set the number of columns etc using col-xx-xx

		foreach($list as $val)
		{
			$this->var = $val;

			if(empty($template['item']))
			{
				$text .= $ns->tablerender('', $this->sc_gallery_thumb('class=gallery_thumb img-responsive img-fluid img-home-portfolio'), 'gallery_portfolio', true);
			}
			else
			{
				$text .= $tp->parseTemplate($template['item'],true,$this);
			}

		}

		if(!empty($template['end']))
		{
			$text .= $tp->parseTemplate($template['end'],true, $this);
		}

		return $text;

	}


	/**
	 * All possible parameters
	 * {GALLERY_SLIDES=4|limit=16&template=MY_SLIDESHOW_SLIDE_ITEM}
	 * first parameter is always number of slides, default is 3
	 * limit - (optional) total limit of pcitures to be shown
	 * template - (optional) template - name of template to be used for parsing the slideshow item
	 */
	function sc_gallery_slides($parm=null)
	{
		$plugPrefs = e107::getPlugConfig('gallery')->getPref();
		$orderBy = varset($plugPrefs['orderby'], 'image_id DESC');

		$tp = e107::getParser();
		$this->slideMode = true;

		$parms         = eHelper::scDualParams($parm);
		$amount        = $parms[1] ? intval($parms[1]) : 3; // vartrue(e107::getPlugPref('gallery','slideshow_perslide'),3);
		$parms         = $parms[2];
		$limit         = (integer) vartrue($parms['limit'], 16);
        
		//$list          = e107::getMedia()->getImages('gallery_image|gallery_image_' . $this->sliderCat, 0, $limit, null, $orderBy);

		$list =  e107::getDb()->retrieve('gallery_images', "*", "`gallery_image_active`=1", true);

		$tmpl          = e107::getTemplate('gallery', 'gallery');
		$tmpl          = array_change_key_case($tmpl); // change template key to lowercase (BC fix)
		$tmpl_key      = vartrue($parms['template'], 'slideshow_slide_item');
		$item_template = $tmpl[$tmpl_key]; // e107::getTemplate('gallery','gallery', vartrue($parms['template'], 'SLIDESHOW_SLIDE_ITEM'));
		$catList       = e107::getMedia()->getCategories('gallery');
		$cat           = varset($catList['gallery_' . $this->sliderCat]);

		$count = 1;
		$inner = '';
		foreach($list as $row)
		{
			$this->setVars($row)
				->addVars($cat);

			$inner .= ($count == 1) ? "\n\n<!-- SLIDE " . $count . " -->\n<div class='slide' id='gallery-item-" . $this->slideCount . "'>\n" : "";
			$inner .= "\n\t" . $tp->parseTemplate($item_template, true) . "\n";
			$inner .= ($count == $amount) ? "\n</div>\n\n" : "";

			if($count == $amount)
			{
				$count = 1;
				$this->slideCount++;
			}
			else
			{
				$count++;
			}
		}

		$inner .= ($count != 1) ? "</div><!-- END SLIDES -->" : "";
		return $inner;
	}


	function sc_gallery_jumper($parm=null)
	{
		// echo "SlideCount=".$this->slideCount; 
		if($this->slideCount == 1 && deftrue('E107_DBG_BASIC'))
		{
			return "gallery-jumper must be loaded after Gallery-Slides";
		}

		$text = '';
		for($i = 1; $i < ($this->slideCount); $i++)
		{
			$val = ($parm == 'space') ? "&nbsp;" : $i;
			$text .= '<a href="#" class="gallery-slide-jumper" id="gallery-jumper-' . $i . '">' . $val . '</a>';
		}

		return $text;

	}


	/* MAIN_GALLERY_DESCRIPTION */
	function sc_main_gallery_description($parm=null) {

	}

 
}
