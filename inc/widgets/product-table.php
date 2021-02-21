<?php
namespace UltraAddons\Widget;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Product_Table extends Base{
    
        
        /**
         * Set your widget name keyword
         *
         *
         * @since 1.0.0
         * @access public
         *
         * @return string keywords
         */
        public function get_keywords() {
            return [ 'ultraaddons', 'table', 'woo', 'product', 'wpt table', 'wc', 'tbl' ]; //'ultraaddons eicon-table'
        }

        /**
         * Retrieve the list of scripts the counter widget depended on.
         *
         * Used to set scripts dependencies required to run the widget.
         *
         * @since 1.0.0.13
         * @access public
         *
         * @return array Widget scripts dependencies.
         * @by Saiful
         */
        public function get_script_depends() {
                return [ 'jquery','select2' ];
        }

        /**
         * Whether the reload preview is required or not.
         *
         * Used to determine whether the reload preview is required.
         *
         * @since 1.0.0
         * @access public
         *
         * @return bool Whether the reload preview is required.
         */
        public function is_reload_preview_required() {
                return true;
        }

	
	/**
	 * Register oEmbed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

                //For General/Content Tab
		$this->content_general();
                
                //For Typography Section Style Tab
                $this->style_table_head();
                
                //For Typography Section Style Tab
                $this->style_table_body();
                
                
	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
            
            $settings = $this->get_settings_for_display();
            $table_id = isset( $settings['table_id'] ) && !empty( $settings['table_id'] ) ? $settings['table_id'] : false;
            if( $table_id && is_numeric( $table_id ) ){
                $name = get_the_title( $table_id );
                $shortcode = "[Product_Table id='{$table_id}' name='{$name}']";
                $shortcode = do_shortcode( shortcode_unautop( $shortcode ) );
		?>
                <div class="wpt-elementor-wrapper wpt-elementor-wrapper-<?php echo esc_attr( $table_id ); ?>">
                    <?php echo $shortcode; ?>
                </div>
		<?php
            }else{
                echo '<h2 class="wpt_elmnt_select_note">';
                echo esc_html__( 'Please select a Table.', 'wpt_pro' );
                echo '</h2>';
            }
	}
        
        protected function content_general() {
                $this->start_controls_section(
			'general',
			[
				'label' => __( 'General', 'wpt_pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
                
                $args = array(
                    'post_type' => 'wpt_product_table',
                    'posts_per_page'=> '-1',
                    'post_status' => 'publish',
                );
                $productTable = new \WP_Query( $args );
                $table_options = array();
                $wpt_extra_msg = false;
                if ($productTable->have_posts()) : 
                    
                    while ($productTable->have_posts()): $productTable->the_post();

                    $id = get_the_id();
                    $table_options[$id] = get_the_title();
                    endwhile;

                else:
                    $table_options = false;
                    //Controls_Manager::HEADING
                endif;
                
		
                wp_reset_postdata();
                wp_reset_query();
                if( $table_options && is_array( $table_options ) ){
                    $this->add_control(
                            'table_id',
                            [
                                    'label' => __( 'Table List', 'wpt_pro' ),
                                    'type' => Controls_Manager::SELECT,
                                    'options' => $table_options,
                                    //'default' => '',
                            ]
                    );
                    
                }else{
                    $wpt_extra_msg = __( 'There is not founded any table to your. ', 'wpt_pro' );
                }
                
                $this->add_control(
                        'table_notification',
                        [
                            'label' => __( 'Additional Information', 'wpt_pro' ),
                            'type' => Controls_Manager::RAW_HTML,
                            'raw' => $wpt_extra_msg . sprintf( 
                                    __( 'Create %sa new table%s.', 'wpt_pro' ), 
                                    '<a href="' . admin_url( 'post-new.php?post_type=wpt_product_table' ) . '">',
                                    '</a>'
                                    ),
                            'content_classes' => 'wpt_elementor_additional_info',
                        ]
                );
                
		$this->end_controls_section();

        }
        
        /**
         * Typography Section for Style Tab
         * 
         * @since 1.0.0.9
         */
        protected function style_table_head() {
            $this->start_controls_section(
                'thead',
                [
                    'label'     => esc_html__( 'Table Head', 'medilac' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                            'name' => 'thead_typography',
                            'global' => [
                                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                            ],
                            'selector' => '{{WRAPPER}} table.wpt_product_table thead tr th',
                    ]
            );

            $this->add_control(
                'thead-color',
                [
                    'label'     => __( 'Color', 'medilac' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.wpt_product_table thead tr th' => 'color: {{VALUE}}',
                    ],
                    'default'   => '#ffffff',
                ]
            );
            
            $this->add_control(
                'thead-bg-color',
                [
                    'label'     => __( 'Background Color', 'medilac' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.wpt_product_table thead tr th' => 'background-color: {{VALUE}}',
                    ],
                    'default'   => '#0a7f9c',
                ]
            );
            
            
            
            $this->end_controls_section();
        }
    
        
        
        /**
         * Typography Section for Style Tab
         * 
         * @since 1.0.0.9
         */
        protected function style_table_body() {
            
            
            $this->start_controls_section(
                'tbody',
                [
                    'label'     => esc_html__( 'Table Body', 'medilac' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                            'name' => 'tbody_typography',
                            'global' => [
                                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} table.wpt_product_table tbody tr td',
                                '{{WRAPPER}} table.wpt_product_table tbody tr td a',
                                '{{WRAPPER}} table.wpt_product_table tbody tr td p',
                                '{{WRAPPER}} table.wpt_product_table tbody tr td div',
                            ],
                    ]
            );

            $this->add_control(
                'tbody-text-color',
                [
                    'label'     => __( 'Text Color', 'medilac' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.wpt_product_table tbody tr td' => 'color: {{VALUE}}',
                        '{{WRAPPER}} table.wpt_product_table tbody tr td p' => 'color: {{VALUE}}',
                        '{{WRAPPER}} table.wpt_product_table tbody tr td div' => 'color: {{VALUE}}',
                    ],
                    'default'   => '#535353',
                ]
            );
            
            $this->add_control(
                'tbody-title-color',
                [
                    'label'     => __( 'Product Title Color', 'medilac' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.wpt_product_table tbody tr td .product_title a' => 'color: {{VALUE}}',
                    ],
                    'default'   => '#000',
                ]
            );
            
            
            $this->add_control(
                'tbody-bg-color',
                [
                    'label'     => __( 'Background Color', 'medilac' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} table.wpt_product_table tbody tr td' => 'background-color: {{VALUE}}',
                    ],
                    //'default'   => '#fff',
                ]
            );
            
            $this->end_controls_section();
        }
    

}
