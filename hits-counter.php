<?php

/*
Plugin Name: Post Hits Counter
Plugin URI: https://github.com/bluesuiter/Hits-Counter
Description: A simple plugin for capturing hits on posts of your wordpress blog. Install it and see the hit records in your Admin Panel.
Version: 2.0.23
Author: Script-Recipes
Author URI: https://scriptrecipes.blogspot.in/2017/11/post-hits-counter.html
Donate link: 
License: GPL2
*/

if (!defined('ABSPATH')) {
    die;
}

try {
    if (file_exists(dirname(__FILE__) . '/helper.php')) {
        require_once(dirname(__FILE__) . '/helper.php');
    }

    if (file_exists(dirname(__FILE__) . '/database/database.php')) {
        require_once(dirname(__FILE__) . '/database/database.php');
        $phcDataBase = new phcDataBaseClass();
        register_activation_hook(__FILE__, array($phcDataBase, 'phcInstallDataTables'));
        add_action('plugins_loaded', array($phcDataBase, 'phcUpdatesCheck'));
    }

    if (file_exists(dirname(__FILE__) . '/admin/PostHitCountClass.php')) {
        require_once(dirname(__FILE__) . '/admin/PostHitCountClass.php');
        require_once(dirname(__FILE__) . '/admin/ShowPostListingClass.php');
        
        $postHitCounter = new PostHitCount();
        add_action('admin_menu', array($postHitCounter, 'phcAdmin'));
        add_action('wp_footer', array($postHitCounter, 'phcCounterAjax'));
        add_shortcode('phcTotalHit', array($postHitCounter, 'phcTotalHitsCount'));
        add_action('wp_ajax_nopriv_post_read', [$postHitCounter, 'phcCallCounter']);
        add_action('wp_ajax_nopriv_rcrd_srch_query', [$postHitCounter, 'saveSearchQuery']);
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
