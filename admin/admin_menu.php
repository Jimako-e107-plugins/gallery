<?php


e107::lan('gallery', true, true);


class spgallery_adminArea extends e_admin_dispatcher
{

    protected $modes = array(

        'prefs'    => array(
            'controller'     => 'spg_prefs_ui',
            'path'             => null,
            'ui'             => 'spg_prefs_form_ui',
            'uipath'         => null
        ),

 
        'gallery'    => array(
            'controller'     => 'galleries_ui',
            'path'             => null,
            'ui'             => 'galleries_form_ui',
            'uipath'         => null
        ),


        'other1'    => array(
            'controller'     => 'spg__images_ui',
            'path'             => null,
            'ui'             => 'spg__images_form_ui',
            'uipath'         => null
        ),
 

    );


    protected $adminMenu = array(
        'prefs/prefs'         => array('caption' => SPG_MENU_PREFS, 'perm' => 'P', 'url' => 'admin_config.php'),
 
        'gallery/list'            => array('caption' => SPG_MENU_MANAGE_002, 'perm' => 'P', 'url' => 'admin_galleries.php'),


        'other1/list'            => array('caption' => SPG_MENU_MANAGE_003, 'perm' => 'P', 'url' => 'admin_config.php'),
        'other1/create'        => array('caption' => LAN_CREATE, 'perm' => 'P', 'url' => 'admin_config.php'),



        // 'main/div0'      => array('divider'=> true),
        // 'main/custom'		=> array('caption'=> 'Custom Page', 'perm' => 'P'),

    );

    protected $adminMenuAliases = array(
        'main/edit'    => 'main/list'
    );

    protected $menuTitle = SPG_MENU_TITLE;
}
