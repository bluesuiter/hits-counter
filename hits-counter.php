<?php

/*
Plugin Name: Hits Counter
Plugin URI: https://github.com/bluesuiter/Hits-Counter
Description: 
Version: 0.9.17
Author: Script-Recipes
Author URI: https://www.facebook.com/Script-Recipes-252174671855204/
Donate link: 
License: GPL2
*/

if(!defined('ABSPATH'))
{
    die;
}

if(file_exists(dirname(__FILE__) . '/database/database.php'))
{
    require_once(dirname(__FILE__) . '/database/database.php');
    $_hcDataBase = new _hcDataBaseClass();
    register_activation_hook(__FILE__, array($_hcDataBase, '_hcInstallDataTables'));
}

if(file_exists(dirname(__FILE__) . '/admin/PostHitCountClass.php'))
{
    require_once(dirname(__FILE__) . '/admin/PostHitCountClass.php');
    $postHitCounter = new PostHitCount();
    add_action('admin_menu', array($postHitCounter, 'counterAdmin'));
    add_action('wp_footer', array($postHitCounter, 'callCounter'));
}