<?php
 
class My_Widget_slider_about extends \Elementor\Widget_Base {
  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
    wp_enqueue_style( 'swiper_css', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css', null); 
    wp_enqueue_style( 'poppins', 'https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap', null);
 
    wp_enqueue_style( 'slider-about-style',plugins_url() . '/zyrgon-glosario-elementor/assets/css/slider-about.css',array(),null); 
    wp_register_script('custom_zyrgon_script_extra_swiper', '/wp-content/plugins/elementor/assets/lib/swiper/v8/swiper.min.js' , array('jquery'),null, true);
    wp_enqueue_script( 'slider-about-script',plugins_url() . '/zyrgon-glosario-elementor/assets/js/slider-about.js',array(),null); 
    wp_enqueue_script('custom_zyrgon_script_extra_swiper');
 
  }
 
  public function get_script_depends() {
    return [
      'widget-slider-script'
    ];
  }
  public function get_style_depends() {
    return [
      'widget-slider-style'
    ];
  }
 
  public function get_name() {
    return 'zyrgon-slider-about';
  }
 
  public function get_title() {
    return __( 'Zyrgon slider about', 'plugin-name' );
  }
 
  public function get_icon() {
    return 'eicon-icon-box';
  }
 
  public function get_categories() {
    return [ 'basic' ];
  }
 
 
  protected function _register_controls() {
    $this->start_controls_section(
      'slider',
      [
        'label' => __( 'slider', 'plugin-name' ),
      ]
    );
 
 
    $repeater = new \Elementor\Repeater();

 
 
    $repeater->add_control(
      'image_item',
      [
        'label' => __( 'Imagem', 'plugin-name' ),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => '',  
        ],
      ]
    );
 
    $repeater->add_control(
      'title_item',
      [
        'label' => __( 'Título', 'plugin-name' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => __( 'Título predeterminado', 'plugin-name' ),
      ]
    );
 
    $repeater->add_control(
      'description_item',
      [
        'label' => __( 'Descrição', 'plugin-name' ),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => __( 'Descrição predeterminado', 'plugin-name' ),
      ]
    );
 
    $this->add_control(
      'list',
      [
        'label' => esc_html__( 'Lista de itens', 'elementor-pro' ),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'image_item' => esc_html__( 'Item', 'elementor-pro' ),
            'title_item' => esc_html__( 'Item', 'elementor-pro' ),
            'description_item' => esc_html__( 'Item', 'elementor-pro' ),
          ],
        ],
        'title_field' => '{{{ title_item }}}',
      ]
    );
 
 
 
 $repeater2 = new \Elementor\Repeater();
 
 
 $repeater2->add_control(
      'image_box',
      [
        'label' => __( 'Imagem', 'plugin-name' ),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => '',  
        ],
      ]
    );
 
  $repeater2->add_control(
      'title_box',
      [
        'label' => __( 'Título', 'plugin-name' ),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => __( 'Título predeterminado', 'plugin-name' ),
      ]
    );
 
 
 $this->add_control(
      'caixa-imagem',
      [
        'label' => esc_html__( 'Caixa imagem', 'elementor-pro' ),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater2->get_controls(),
        'default' => [
          [
            'image_box' => esc_html__( 'Item', 'elementor-pro' ),
            'title_box' => esc_html__( 'Item', 'elementor-pro' ),
          ],
        ],
        'title_field' => '{{{ title_item }}}',
      ]
    );
    
 
    $this->end_controls_section();
 
  }
 protected function render() {
    $settings = $this->get_settings_for_display();
    
    $html = ''; // Inicialize a variável $html antes do loop
    
            $html .= '<div class="content-box">';

    if (!empty($settings['caixa-imagem'])) {
        foreach ($settings['caixa-imagem'] as $item) {
            $html .= '<div class="caixa-imagem">'; // Caixa de imagem
            $html .= '<img class="image-box-about" src="' . esc_url($item['image_box']['url']) . '" alt="' . esc_attr($item['title_box']) . '" />';
            $html .= '<h2 class="title-box-about">' . esc_html($item['title_box']) . '</h2>';
            $html .= '</div>'; // .caixa-imagem
        }
    }
    $html .= '</div>';
    
    // Renderizar o slider
    $html .= '<div class="swiper-container slider_about">';
    $html .= '<div class="swiper-wrapper">';

    foreach ($settings['list'] as $item) {
        $html .= '<div class="swiper-slide">';
        $html .= '<div class="content2">';
        $html .= '<img class="image-about" src="' . esc_url($item['image_item']['url']) . '" alt="' . esc_attr($item['title_item']) . '" />';
        $html .= '<div class="content-text2">';
        $html .= '<h2 class="title-about">' . esc_html($item['title_item']) . '</h2>';
        $html .= '<p class="description-about">' . esc_html($item['description_item']) . '</p>';
        $html .= '</div>'; // .content-text
        $html .= '</div>'; // .content
        $html .= '</div>'; // .swiper-slide
    }

    $html .= '</div>'; // .swiper-wrapper
 
    $html .= '<div class="swiper-button-prev slider-about-navigation">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="40" viewBox="0 0 24 40" fill="none">
                  <path d="M20 36L4 20L20 4" stroke="#FF4713" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
</div>';
    $html .= '<div class="swiper-button-next slider-about-navigation">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="40" viewBox="0 0 25 40" fill="none">
                <path d="M4.75 4L20.75 20L4.75 36" stroke="#FF4713" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg></div>';
                
    $html .= '</div>'; // .swiper-container
    
    echo $html;
}
    
}