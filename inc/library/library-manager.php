<?php
namespace UltraAddons\Library;

defined('ABSPATH') || die();

class Library_Manager{
    
    protected static $source = null;
    
    private static $assets;
    private static $dir;
    
    
    public static function init(){
    
        self::$dir = dirname(__FILE__) . '/';
        
        //Handle Assets (CSS and JavaScript) File handle/manage. Loading on Screen and Preview Page
        self::asset_manage();

//        add_action( 'elementor/init', [__CLASS__, 'testing'] );

        add_action( 'elementor/editor/footer', [__CLASS__, 'render_panel_html'] );
    }
    
    public static function render_panel_html(){
        //var_dump(__DIR__ . '/templates/panel.php');
        include __DIR__ . '/templates/test.php';
    }
    
    public static function testing(){
        \Elementor\Plugin::instance()->templates_manager->register_source( '\UltraAddons\Library\Library_Source' );
    }
    
    
    
    
    public static function asset_manage() {
        self::$assets = trailingslashit( ULTRA_ADDONS_URL . 'inc/library/assets' );
        
        //enqueue editor js for elementor.
        add_action( 'elementor/editor/before_enqueue_scripts', [__CLASS__, 'elementor_editor_before_enqueue'], 1 );
        
        //Style CSS code for Elementor Screen
        add_action( 'elementor/editor/after_enqueue_styles', [__CLASS__, 'elementor_editor_after_style'], 1 );
        
        //Style CSS code for Elementor Screen 
        add_action( 'elementor/preview/enqueue_styles', [__CLASS__, 'elementor_preview_style'], 1 );
        
    }
    
    /**
     * Style CSS code For Elementor Preview Modal Page
     * 
     * @since 1.0.4.0
     */
    public static function elementor_preview_style(){
        wp_enqueue_style( 
                'ultraaddons-library-preview-style', 
                self::$assets . 'css/preview.css', 
                array(), 
                ULTRA_ADDONS_VERSION
        );
        
    }
    
    
    /**
     * Style CSS code For Elementor Screen
     * 
     * @since 1.0.4.0
     */
    public static function elementor_editor_after_style(){
        wp_enqueue_style( 
                'ultraaddons-library-editor-screen', 
                self::$assets . 'css/editor.css', 
                array(), 
                ULTRA_ADDONS_VERSION
        );
        
    }
    
    /**
     * JavaScript For Elementor Screen
     * 
     * @since 1.0.4.0
     */
    public static function elementor_editor_before_enqueue(){
        wp_enqueue_script( 
                'ultraaddons-library-editor-script', 
                self::$assets . 'js/editor.js', 
                array('jquery', 'underscore', 'backbone-marionette'), 
                ULTRA_ADDONS_VERSION,
                true
        );
    }
}