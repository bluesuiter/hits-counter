<?php

/*
Plugin Name: Hits Counter
Plugin URI: https://github.com/bluesuiter/Hits-Counter
Description: A simple plugin for capturing hits on posts of your wordpress blog. Install it and see the hit records in your Admin Panel.
Version: 0.11.17
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
    $phcDataBase = new phcDataBaseClass();
    register_activation_hook(__FILE__, array($phcDataBase, 'phcInstallDataTables'));
}

if(file_exists(dirname(__FILE__) . '/admin/PostHitCountClass.php'))
{
    require_once(dirname(__FILE__) . '/admin/PostHitCountClass.php');
    $postHitCounter = new PostHitCount();
    add_action('admin_menu', array($postHitCounter, 'phcAdmin'));
    add_action('wp_footer', array($postHitCounter, 'phcCallCounter'));
}


function is_post_type()
{
    global $post;
    $postTypes = unserialize(get_option('post_type_hits_cout_'));
    $postType = $post->post_type;

    if(isset($postTypes[$postType]) && $postTypes[$postType]==1)
    {
        return true;
    }
    return false;
}


function phcGetFromUrl($key)
{
    if (isset($_GET[$key]))
    {
        return esc_html($_GET[$key]);
    }
    elseif (isset($_POST[$key]))
    {
        return esc_html($_POST[$key]);
    }
    return false;
}

