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
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Card extends Base{
    
    /**
     * Get your widget name
     *
     * Retrieve oEmbed widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string keywords
     */
    public function get_keywords() {
        return [ 'ultraaddons', 'card', 'info', 'box' ];
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
        
        $this->content_general_controls();
        $this->style_title_controls();
        $this->style_description_controls();

       
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
        $settings           = $this->get_settings_for_display();
        $title = $settings['title'];
        $description = $settings['description'];
        $image = $settings['image']['url'];
        
        
        ?>
        <div class="ua-card-wrapper">
            <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" style="width:100%">
            <div class="container">
                <h4><b><?php echo esc_html( $title ); ?></b></h4> 
                <p><?php echo esc_html( $description ); ?></p> 
            </div>
        </div>   
        <?php
        
    }
    
        
    /**
     * General Section for Content Controls
     * 
     * @since 1.0.0.9
     */
    protected function content_general_controls() {
        $this->start_controls_section(
            'general',
            [
                'label'     => esc_html__( 'General', 'medilac' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'title',
                [
                    'label'         => esc_html__( 'Title or Name', 'medilac' ),
                    'type'          => Controls_Manager::TEXT,
                    'default'       => '',
                    'label_block'   => TRUE,
                    'dynamic'       => ['active' => true],
                ]
        );
        
        $this->add_control(
            'description',
                [
                    'label'         => esc_html__( 'Description', 'medilac' ),
                    'type'          => Controls_Manager::TEXT,
                    'default'       => '',
                    'label_block'   => TRUE,
                    'dynamic'       => ['active' => true],
                ]
        );
        
        $this->add_control(
                        'image',
                        [
                                'label' => __( 'Profile Picture', 'ultraaddons' ),
                                'type' => Controls_Manager::MEDIA,
                                'default' => [
                                        'url' => Utils::get_placeholder_image_src(),
                                ],
                                'dynamic' => [
                                        'active' => true,
                                ],


                        ]
                );
        
        
        $this->end_controls_section();
    }
    
        
    /**
     * General Section for Content Controls
     * 
     * @since 1.0.0.9
     */
    protected function style_title_controls() {
        $this->start_controls_section(
            'style_title',
            [
                'label'     => esc_html__( 'Title', 'medilac' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );
        
        
        $this->add_control(
            'title_color',
            [
                'label'     => __( 'Color', 'medilac' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ua-card-wrapper h4' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .ua-card-wrapper h4',
            ]
        );
        
        $this->end_controls_section();
    }
    /**
     * General Section for Content Controls
     * 
     * @since 1.0.0.9
     */
    protected function style_description_controls() {
        $this->start_controls_section(
            'description_style',
            [
                'label'     => esc_html__( 'Description', 'medilac' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );
        
        
        $this->add_control(
            'description_color',
            [
                'label'     => __( 'Color', 'medilac' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ua-card-wrapper p' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'description_typography',
                'selector' => '{{WRAPPER}} .ua-card-wrapper p',
            ]
        );
        
        $this->end_controls_section();
    }
    
}