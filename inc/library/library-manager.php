<?php
namespace UltraAddons\Library;

defined('ABSPATH') || die();

class Library_Manager{
    
    public static function init(){
        //var_dump(\Elementor\Plugin::instance()->templates_manager);
        
        add_action( 'elementor/init', [__CLASS__, 'testing'] );
    }
    
    
    public static function testing() {
//        var_dump(\Elementor\Plugin::instance()->templates_manager->add_actions());
//        \Elementor\Plugin::instance()->templates_manager->unregister_source('remote');
        
        }
}