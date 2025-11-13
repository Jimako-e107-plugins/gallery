<?php

// Generated e107 Plugin Admin Area 

require_once('../../../class2.php');
if (!getperms('P')) 
{
	e107::redirect('admin');
	exit;
}

require_once("admin_menu.php");

class spg_prefs_ui extends e_admin_ui
{
			
		protected $pluginTitle		= LAN_PLUGIN_GALLERY_TITLE;
		protected $pluginName		= 'gallery';
 
	
		protected $fields 		= array ( 		);		
 

	    protected $preftabs        = array(SPG_CG_FRONTEND_PAGE, SPG_CG_GENERAL);
		protected $prefs = array(
			'page_title'		=> array('title'=> SPG_PREFS_NAME, 'tab'=>0, 'type'=>'text', 'data' => 'str', 'help'=>'', 'writeParms' => []),
			'display_title'		=> array('title'=> SPG_PREFS_DISPLAYTITLE, 'tab'=>0, 'type'=>'boolean', 'data' => 'str', 'help'=>'', 'writeParms' => []),
			'thumbnails'		=> array('title'=> SPG_PREFS_THUMB, 'nolabel'=>1, 'tab'=>0, 'type'=>'method', 'data' => 'array',   'writeParms' => []),
			'images'		=> array('title'=> SPG_PREFS_IMAGE, 'tab'=>0, 'type'=> 'method', 'data' => 'array',   'writeParms' => []),
			'perrow'		=> array('title'=> SPG_PREFS_PERROW, 'tab'=>0, 'type'=>'radio', 'data' => 'str', 'help'=>'', 'writeParms' => []),
			'perpage'		=> array('title'=> SPG_PREFS_PERPAGE, 'tab'=>0, 'type'=> 'radio', 'data' => 'str', 'help'=>'', 'writeParms' => []),
			'page_description'		=> array('title'=> SPG_PREFS_DESCRIPTION, 'tab'=>0, 'type'=>'bbarea', 'data' => 'str', 'help'=> SPG_PREFS_DESCRIPTION_HELP, 'writeParms' => []),

			'display_breadcrumbs' => array(
				'title'=> SPG_CG_DISPLAY_BREADCRUMBS, 
				'tab'=>1, 
				'type'=>'boolean', 
				'data' => 'str', 
				'help'=>SPG_CG_DISPLAY_BREADCRUMBS_HELP, 
				'writeParms' => []
			),
			'downloadable'               => array(
					'title' => LAN_GALLERY_ADMIN_08,
					'tab'   => 1,
					'type'  => 'boolean',
					'data'  => 'int',
					'help'  => LAN_GALLERY_ADMIN_09,
			),
		); 

	
		public function init()
		{
 
			$this->prefs['page_title']['writeParms']['size'] = "block-level";
			$perrow = array(1=>1, 2=>2, 3=>3, 4=>4, 6=>6, 12=>12);

			$this->prefs['perrow']['writeParms']['optArray'] = $perrow;
			//6,12,24,36,48,60,72,84,96,"all"
			$perpage = array(6 => 6, 12 => 12, 24 => 24, 36 => 36, 60 => 60, 72 => 72, 84 => 84, 96 => 96, 'all'=> 'all');
			$this->prefs['perpage']['writeParms']['optArray'] = $perpage;
		}

 		
		
		// left-panel help menu area. (replaces e_help.php used in old plugins)
		public function renderHelp()
		{
			$caption = LAN_HELP;
			$text = 'Some help text';

			return array('caption'=>$caption,'text'=> $text);

		}
			
 
			
}
				


class spg_prefs_form_ui extends e_admin_form_ui
{

	function images($curVal, $mode)
	{

		switch ($mode)
		{
			case 'write': // Edit Page

				$thumbnailPrefs = is_array($curVal) ? $curVal : ['width' => 1920, 'height' => 1920, 'crop'=>1];

				// Thumbnail Size
				$spg_prefs  = "<table class='table adminform'>";
				$spg_prefs .= "<tr>";
				$spg_prefs .= "<td class='forumheader2' width='25%'>" . SPG_PREFS_IMAGE . ":<div class='smalltext'>" . SPG_PREFS_IMAGE_HELP . "</div></td>";
				$spg_prefs .= "<td class='forumheader3'>";
				$spg_prefs .= $this->text('images[width]', $thumbnailPrefs["width"], 4, 'size=4');

				$spg_prefs .=  " x ";
				$spg_prefs .= $this->text('images[height]', $thumbnailPrefs["height"], 4, 'size=4');


				$spg_prefs .= "</tr>";

				// Thumbnail Crop
				$spg_prefs .= "<tr>";
				$spg_prefs .= "<td class='forumheader2' width='25%'>" . SPG_PREFS_IMAGECROP . ":<div class='smalltext'>" . SPG_PREFS_IMAGECROP_HELP . "</div></td>";
				$spg_prefs .= "<td class='forumheader3'>";
				$spg_prefs .=  $this->renderElement("images[crop]",  $thumbnailPrefs["crop"], array('type' => 'boolean'));
				$spg_prefs .=  "</td>";
				$spg_prefs .= "</tr>";
				$spg_prefs .= "</table>";
				//$text = $this->text('thumbnails', $curVal, 255, 'size=large');
				return $spg_prefs;
				break;
		}

		return null;
	}

	function thumbnails($curVal = array(), $mode)
	{
 
		// Ensure $curVal is an array with default values if empty or not an array
        $thumbnailPrefs = is_array($curVal) ? $curVal : ['width' => 100, 'height' => 100, 'crop'=>1];

		switch ($mode)
		{
			case 'write': // Edit Page
				// Thumbnail Size
				$spg_prefs  = "<table class='table adminform'>";
				$spg_prefs .= "<tr>";
				$spg_prefs .= "<td class='forumheader2' width='25%'>" . SPG_PREFS_THUMB . ":<div class='smalltext'>" . SPG_PREFS_THUMB_HELP . "</div></td>";
			 	$spg_prefs .= "<td class='forumheader3'>" ;
				$spg_prefs .= $this->text('thumbnails[width]', $thumbnailPrefs['width'], 4, 'size=4');
	 
				$spg_prefs .=  " x " ;
				$spg_prefs .= $this->text('thumbnails[height]', $thumbnailPrefs["height"], 4, 'size=4');

		 
				$spg_prefs .= "</tr>";

				// Thumbnail Crop
				$spg_prefs .= "<tr>";
				$spg_prefs .= "<td class='forumheader2' width='25%'>" . SPG_PREFS_THUMBCROP . ":<div class='smalltext'>" . SPG_PREFS_THUMBCROP_HELP . "</div></td>";
			 	$spg_prefs .= "<td class='forumheader3'>";
				$spg_prefs .=  $this->renderElement("thumbnails[crop]",  $thumbnailPrefs["crop"] , array('type'=>'boolean'))  ;
				$spg_prefs .=  "</td>";
				$spg_prefs .= "</tr>";
				$spg_prefs .= "</table>";
				//$text = $this->text('thumbnails', $curVal, 255, 'size=large');
				return $spg_prefs;
				break;
		}

		return null;
	}
	 
}		
		

				
	

				
class spg__images_ui extends e_admin_ui
{
			
		protected $pluginTitle		= 'Gallery';
		protected $pluginName		= 'spgallery';
	//	protected $eventName		= 'spgallery-spg__images'; // remove comment to enable event triggers in admin. 		
		protected $table			= 'spg__images';
		protected $pid				= 'image_id';
		protected $perPage			= 10; 
		protected $batchDelete		= true;
		protected $batchExport     = true;
		protected $batchCopy		= true;

	//	protected $sortField		= 'somefield_order';
	//	protected $sortParent      = 'somefield_parent';
	//	protected $treePrefix      = 'somefield_title';

	//	protected $tabs				= array('tab1'=>'Tab 1', 'tab2'=>'Tab 2'); // Use 'tab'=>'tab1'  OR 'tab'=>'tab2' in the $fields below to enable. 
		
	//	protected $listQry      	= "SELECT * FROM `#tableName` WHERE field != '' "; // Example Custom Query. LEFT JOINS allowed. Should be without any Order or Limit.
	
		protected $listOrder		= 'image_id DESC';
	
		protected $fields 		= array (
			'checkboxes'              => array ( 'title' => '', 'type' => null, 'data' => null, 'width' => '5%', 'thclass' => 'center', 'forced' => 'value', 'class' => 'center', 'toggle' => 'e-multiselect', 'readParms' => [], 'writeParms' => [],),
			'image_id'                => array ( 'title' => LAN_ID, 'type' => 'number', 'data' => 'int', 'width' => '5%', 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
			'image_gallery'           => array ( 'title' => 'Gallery', 'type' => 'dropdown', 'data' => 'int', 'width' => 'auto', 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left', 'filter' => false, 'batch' => false,),
			'image_file'              => array ( 'title' => 'File', 'type' => 'image', 'data' => 'safestr', 'width' => 'auto', 'help' => '', 'readParms' => 'thumb=80x80', 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
			'image_summary'           => array ( 'title' => LAN_TITLE, 'type' => 'text', 'data' => 'safestr', 'width' => 'auto', 'inline' => true, 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
			'image_description'       => array ( 'title' => LAN_DESCRIPTION, 'type' => 'textarea', 'data' => 'str', 'width' => '40%', 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
			'image_active'            => array ( 'title' => 'Active', 'type' => 'boolean', 'data' => 'int', 'width' => 'auto', 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
			'image_order'             => array ( 'title' => LAN_ORDER, 'type' => 'number', 'data' => 'int', 'width' => 'auto', 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
			'image_created'           => array ( 'title' => 'Created', 'type' => 'datestamp', 'data' => 'int', 'width' => 'auto', 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
			'options'                 => array ( 'title' => LAN_OPTIONS, 'type' => null, 'data' => null, 'width' => '10%', 'thclass' => 'center last', 'class' => 'center last', 'forced' => 'value', 'readParms' => [], 'writeParms' => [],),
		);		
		
		protected $fieldpref = array('image_summary');
		
	
		public function init()
		{

		// Set drop-down values (if any). 
		// === POPULATE image_gallery DROPDOWN FROM DATABASE ===
		$galleryOptions = array(0 => '-- ' . SPG_JS_SELECT_NONE . ' --');

		$sql = e107::getDb();
		if ($sql->select('spg__galleries', 'gallery_id, gallery_title', ' TRUE ORDER BY gallery_title ASC', true))
		{
			while ($row = $sql->fetch())
			{
				$galleryOptions[$row['gallery_id']] = $row['gallery_title'];
			}
		}

		// Apply to both Edit/Create form AND filter dropdown in list
		$this->fields['image_gallery']['writeParms']['optArray'] = $galleryOptions;
		$this->fields['image_gallery']['readParms']['optArray']  = $galleryOptions;	
		}

		// ------- Customize Create --------
		
		public function beforeCreate($new_data,$old_data)
		{
			return $new_data;
		}
	
		public function afterCreate($new_data, $old_data, $id)
		{
			// do something
		}

		public function onCreateError($new_data, $old_data)
		{
			// do something		
		}		
		
		
		// ------- Customize Update --------
		
		public function beforeUpdate($new_data, $old_data, $id)
		{
			return $new_data;
		}

		public function afterUpdate($new_data, $old_data, $id)
		{
			// do something	
		}
		
		public function onUpdateError($new_data, $old_data, $id)
		{
			// do something		
		}		
		
		// left-panel help menu area. (replaces e_help.php used in old plugins)
		public function renderHelp()
		{
			$caption = LAN_HELP;
			$text = 'Some help text';

			return array('caption'=>$caption,'text'=> $text);

		}
			
	/*	
		// optional - a custom page.  
		public function customPage()
		{
			if($this->getPosted('custom-submit')) // after form is submitted. 
			{
				e107::getMessage()->addSuccess('Changes made: '. $this->getPosted('example'));
			}

			$this->addTitle('My Custom Title');


			$frm = $this->getUI();
			$text = $frm->open('my-form', 'post');

				$tab1 = "<table class='table table-bordered adminform'>
					<colgroup>
						<col class='col-label'>
						<col class='col-control'>
					</colgroup>
					<tr>
						<td>Label ".$frm->help('A help tip')."</td>
						<td>".$frm->text('example', $this->getPosted('example'), 80, ['size'=>'xlarge'])."</td>
					</tr>
					</table>";

			// Display Tab
			$text .= $frm->tabs([
				'general'   => ['caption'=>LAN_GENERAL, 'text' => $tab1],
			]);

			$text .= "<div class='buttons-bar text-center'>".$frm->button('custom-submit', 'submit', 'submit', LAN_CREATE)."</div>";
			$text .= $frm->close();

			return $text;
			
		}
			
		
		
	*/
			
}
				


class spg__images_form_ui extends e_admin_form_ui
{

	
	// Custom Method/Function (pref)
	function thumbnails($curVal,$mode)
	{

		 		
		switch($mode)
		{			
			case 'write': // Edit Page
			
				return $this->text('thumbnails',$curVal, 255, 'size=large');
			break;
			
		}
		
		return null;
	}

	
	// Custom Method/Function (pref)
	function images($curVal,$mode)
	{

		 		
		switch($mode)
		{			
			case 'write': // Edit Page
			
				return $this->text('images',$curVal, 255, 'size=large');
			break;
			
		}
		
		return null;
	}

}		
		
		
new spgallery_adminArea();

require_once(e_ADMIN."auth.php");
e107::getAdminUI()->runPage();

require_once(e_ADMIN."footer.php");
exit;
