<?php
namespace UltraAddons\Library;

use Elementor\Core\Common\Modules\Ajax\Module as Ajax;

defined('ABSPATH') || die();

class Library_Manager{
    
    protected static $source = null;
    
    private static $assets;
    private static $dir;
    
    

    public static function init(){
        self::$dir = dirname(__FILE__) . '/';
        
        //Handle Assets (CSS and JavaScript) File handle/manage. Loading on Screen and Preview Page
        self::asset_manage();
        
        // print views and tab variables on footer.
        add_action( 'elementor/editor/footer', [__CLASS__, 'admin_inline_js'] );
        add_action( 'elementor/editor/footer', [__CLASS__, 'print_views'] );
        
        
        
        //var_dump(\Elementor\Plugin::instance()->templates_manager);
        
//        add_action( 'elementor/init', [__CLASS__, 'testing'] );
//        add_action( 'elementor/ajax/register_actions', [__CLASS__, 'register_ajax_actions'] );
    }
    
    public static function register_ajax_actions( Ajax $ajax ) {

		$ajax->register_ajax_action( 'get_elementskit_library_data', function( $data ) {
			if ( ! current_user_can( 'edit_posts' ) ) {
				throw new \Exception( 'Access Denied' );
			}

			if ( ! empty( $data['editor_post_id'] ) ) {
				$editor_post_id = absint( $data['editor_post_id'] );

				if ( ! get_post( $editor_post_id ) ) {
					throw new \Exception( __( 'Post not found.', 'elementskit-lite' ) );
				}

				\Elementor\Plugin::instance()->db->switch_to_post( $editor_post_id );
			}

			$result = self::get_library_data( $data );
                        var_dump($result);
			return $result;
		} );

		$ajax->register_ajax_action( 'get_elementskit_template_data', function( $data ) {
			if ( ! current_user_can( 'edit_posts' ) ) {
				throw new \Exception( 'Access Denied' );
			}

			if ( ! empty( $data['editor_post_id'] ) ) {
				$editor_post_id = absint( $data['editor_post_id'] );

				if ( ! get_post( $editor_post_id ) ) {
					throw new \Exception( __( 'Post not found', 'elementskit-lite' ) );
				}

				\Elementor\Plugin::instance()->db->switch_to_post( $editor_post_id );
			}

			if ( empty( $data['template_id'] ) ) {
				throw new \Exception( __( 'Template id missing', 'elementskit-lite' ) );
			}

			$result = self::get_template_data( $data );

			return $result;
		} );
	}    

    public static function testing() {
        var_dump(self::get_source());
        //var_dump(self::get_template_data());
//        var_dump(\Elementor\Plugin::instance()->templates_manager->add_actions());
//        \Elementor\Plugin::instance()->templates_manager->unregister_source('remote');
        
    }
     
    public static function print_views(){
		foreach ( glob( self::$dir . 'views/editor/*.php' ) as $file ) {
			$name = basename( $file, '.php' );
			ob_start();
			include $file;
			printf( '<script type="text/html" id="view-elementskit-%1$s">%2$s</script>', $name, ob_get_clean() );
		}
	}
    public static function admin_inline_js() { ?>
		<script type="text/javascript" >

		var UltraAddonsLibraryData = {
			"libraryButton": "Elements Button",
			"modalRegions": {
				"modalHeader": ".dialog-header",
				"modalContent": ".dialog-message"
			},
			"license": {
				"activated": true,
				"link": ""
			},
			"tabs": {
				"elementskit_page": {
					"title": "Ready Pages",
					"data": [],
					"sources": ["elementskit-theme", "elementskit-api"],
					"settings": {
						"show_title": true,
						"show_keywords": true
					}
				},
				"elementskit_header": {
					"title": "Headers",
					"data": [],
					"sources": ["elementskit-theme", "elementskit-api"],
					"settings": {
						"show_title": false,
						"show_keywords": true
					}
				},
				"elementskit_footer": {
					"title": "Footers",
					"data": [],
					"sources": ["elementskit-theme", "elementskit-api"],
					"settings": {
						"show_title": false,
						"show_keywords": true
					}
				},
				"elementskit_section": {
					"title": "Sections",
					"data": [],
					"sources": ["elementskit-theme", "elementskit-api"],
					"settings": {
						"show_title": false,
						"show_keywords": true
					}
				},
				"elementskit_widget": {
					"title": "Widget Presets",
					"data": [],
					"sources": ["elementskit-theme", "elementskit-api"],
					"settings": {
						"show_title": false,
						"show_keywords": true
					}
				},
				// "local": {
				// 	"title": "My Library",
				// 	"data": [],
				// 	"sources": ["elementskit-local"],
				// 	"settings": []
				// }
			},
			"defaultTab": "elementskit_page"
		};
                console.log(UltraAddonsLibraryData);
		</script> <?php
	}
    
    
    /**
     * Undocumented function
     *
     * @return Library_Source
     */
    public static function get_source() {
            if ( is_null( self::$source ) ) {
                    self::$source = new Library_Source();
            }

            return self::$source;
    }

    public static function get_template_data( array $args ) {
            $source = self::get_source();
            $data = $source->get_data( $args );
            return $data;
    }

    public static function get_library_data( array $args ) {
            $source = self::get_source();

            if ( ! empty( $args['sync'] ) ) {
                    Library_Source::get_library_data( true );
            }

            return [
                    'templates' => $source->get_items(),
                    'tags' => $source->get_tags(),
            ];
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