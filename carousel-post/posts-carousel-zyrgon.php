<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Responsive\Responsive;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use The7\Mods\Compatibility\Elementor\Style\Posts_Masonry_Style;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Arrows;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Bullets;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Aspect_Ratio;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Image_Size;
use The7\Mods\Compatibility\Elementor\With_Post_Excerpt;
use The7_Categorization_Request;
use The7_Query_Builder;
use The7_Related_Query_Builder;

defined( 'ABSPATH' ) || exit;

/**
 * Posts_Carousel class.
 */
class Posts_Carousel_Zyrgon extends Posts {

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7_elements_carousel_zyrgon';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	protected function the7_title() {
		return esc_html__( 'Posts Carousel Zyrgon', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-posts-carousel';
	}

	/**
	 * @return array|string[]
	 */
	public function get_script_depends() {
		if ( $this->is_preview_mode() ) {
			return [ 'the7-elements-carousel-widget-preview' ];
		}

		return [];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-carousel-widget', 'the7-carousel-navigation' ];
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		// Content Tab.
		$this->add_query_content_controls();
		$this->add_layout_content_controls();
		$this->add_content_controls();
		$this->add_scrolling_controls();
		$this->template( Arrows::class )->add_content_controls();
		$this->template( Bullets::class )->add_content_controls();

		// Style Tab.
		$this->add_skin_style_controls();
		$this->add_box_style_controls();
		$this->add_image_style_controls();
		$this->add_hover_icon_style_controls();
		$this->add_content_style_controls();
		$this->add_post_title_style_controls();
		$this->add_post_meta_style_controls();
		$this->add_text_style_controls();
		$this->template( Button::class )->add_style_controls(
			Button::ICON_MANAGER,
			[
				'show_read_more_button' => 'y',
			]
		);
		$this->template( Arrows::class )->add_style_controls();
		$this->template( Bullets::class )->add_style_controls();
	}

	/**
	 * Render widget.
	 */
protected function render() {
    $settings = $this->get_settings_for_display();

    if (!in_array($settings['post_type'], ['current_query', 'related'], true) && !post_type_exists($settings['post_type'])) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo the7_elementor_get_message_about_disabled_post_type();
        return;
    }

    $query = $this->get_query(new The7_Categorization_Request());
    if (!$query->have_posts()) {
        if ($settings['post_type'] === 'current_query') {
            $this->render_nothing_found_message();
        }
        return;
    }

    $this->remove_image_hooks();
    $this->print_inline_css();

    $this->template(Arrows::class)->add_container_render_attributes('wrapper');
    $this->add_container_class_render_attribute('wrapper');
    $this->add_container_data_render_attributes('wrapper');

    echo '<div ' . $this->get_render_attribute_string('wrapper') . '>';

    $is_overlay_post_layout = $this->is_overlay_post_layout($settings);

    while ($query->have_posts()) {
        $query->the_post();

        $this->render_post_custom(); // Renderiza todos os posts usando o método render_post_custom()

    }

    wp_reset_postdata();

    echo '</div>';

    $this->template(Arrows::class)->render();

    $this->add_image_hooks();
}

protected function render_post_custom() {
    $post_id = get_the_ID();
    $post_title = get_the_title($post_id);
    $image_url = get_the_post_thumbnail_url($post_id, 'large');
    $date_format = 'j F Y';
    $post_tags = get_the_tags($post_id);
    $descricao = get_field('descricao');
    $limite_caracteres = 150; // Defina o número máximo de caracteres desejado
    ?>
    <div class="container-card">
        <div class="card-post">
            <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                <div class="image-card-blog-">
                    <?php echo '<img class="image-card-blog" src="' . esc_url($image_url) . '" alt="Imagem" />'; ?>
                </div>
            </a>
    
            <div class="container-card-blog">
        
                <div class="title-card-blog">
            
                    <div class="info-card-blog">
                        <img class="icon-clock" src="/wp-content/themes/zyrgon-child-theme/assets/calendar.png" alt="">
                        <p><?php echo get_the_date( $date_format, $post_id ); ?></p>
                        <img class="icon-clock" src="/wp-content/themes/zyrgon-child-theme/assets/clock.png" alt="">
                        <p><?php echo get_field('tempo_de_leitura') . __(' minutos de leitura', 'zyrgon-child'); ?></p>
                    </div>
                    <?php if ($post_tags) : ?>
                        <div class="etiquetas-card">
                            <?php foreach ($post_tags as $tag) : ?>
                                <span class="etiqueta-card"><?php echo $tag->name; ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <h2 class="post-title-blog-card"><?php echo get_the_title($post_id) ?></h2>
                    <h2 class="post-desc-card-blog"><?php echo $descricao; ?></h2>
                    
                    <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                        <h2 class="card-blog-saber-mais">Saber mais</h2>
                    </a>
                
                </div>
            </div>
        </div>
    </div>
    <?php
}

protected function _register_controls_actions() {
    parent::_register_controls_actions();
    
    // Adicione a ação para registrar os controles do Elementor
    add_action('elementor/element/posts/section_skin/before_section_end', [$this, 'register_skin_controls']);
}

public function register_skin_controls($widget) {
    $widget->start_controls_section(
        'section_skin',
        [
            'label' => __('Skin', 'Card Blog'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]
    );

    $widget->add_control(
        'skin',
        [
            'label'   => __('Skin', 'Card Blog'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'default'         => __('Default', 'Card Blog'),
                'skin_blog-card'  => __('Blog Card Skin', 'Card Blog'), // Corrigido o nome do skin aqui
            ],
            'default' => 'default',
        ]
    );

    $widget->end_controls_section();
}
	/**
	 * @param array $settings Settings array.
	 *
	 * @return false|string
	 */
	protected function get_details_btn( $settings ) {
		// Cleanup button render attributes.
		$this->remove_render_attribute( 'box-button' );

		$link_attributes = $this->get_link_attributes( $settings );

		$tag = 'button';
		if ( $link_attributes['href'] ) {
			$tag                           = 'a';
			$link_attributes['aria-label'] = the7_get_read_more_aria_label();
			$this->add_render_attribute( 'box-button', $link_attributes );
		}

		ob_start();
		$this->template( Button::class )->render_button( 'box-button', esc_html( $settings['read_more_button_text'] ), $tag );

		return ob_get_clean();
	}

	/**
	 * @param string $element Element name.
	 *
	 * @return void
	 */
	protected function add_container_class_render_attribute( $element ) {
		$class = [
			$this->get_unique_class(),
			'owl-carousel',
			'portfolio-carousel-shortcode',
			'elementor-owl-carousel-call',
			'portfolio-shortcode',
			'the7-elementor-widget',
			'content-bg-on',
		];

		$settings = $this->get_settings_for_display();

		$layout_classes = array(
			'classic'           => 'classic-layout-list',
			'bottom_overlap'    => 'bottom-overlap-layout-list',
			'gradient_overlap'  => 'gradient-overlap-layout-list',
			'gradient_overlay'  => 'gradient-overlay-layout-list',
			'gradient_rollover' => 'content-rollover-layout-list',
		);

		$layout = $settings['post_layout'];
		if ( array_key_exists( $layout, $layout_classes ) ) {
			$class[] = $layout_classes[ $layout ];
		}

		$gradient_layout = in_array( $layout, [ 'gradient_overlay', 'gradient_rollover' ], true );

		if ( $gradient_layout ) {
			if ( $settings['post_content'] !== 'show_excerpt' && ! $settings['show_read_more_button'] ) {
				$class[] = 'disable-layout-hover';
			}

			$class[] = 'description-on-hover';
		} else {
			$class[] = 'description-under-image';
		}

		if ( $settings['image_scale_animation_on_hover'] === 'quick_scale' ) {
			$class[] = 'quick-scale-img';
		} elseif ( $settings['image_scale_animation_on_hover'] === 'slow_scale' ) {
			$class[] = 'scale-img';
		}

		if ( ! $settings['post_date'] && ! $settings['post_terms'] && ! $settings['post_comments'] && ! $settings['post_author'] ) {
			$class[] = 'meta-info-off';
		}

		if ( ! $settings['overlay_background_background'] && ! $settings['overlay_hover_background_background'] ) {
			$class[] = 'enable-bg-rollover';
		}

		if ( 'gradient_overlay' === $settings['post_layout'] ) {
			$class[] = presscore_tpl_get_hover_anim_class( $settings['go_animation'] );
		}

		$this->add_render_attribute( $element, 'class', $class );
	}

	/**
	 * @return \WP_Query
	 */
	protected function get_query( $request = null ) {
		$settings  = $this->get_settings_for_display();
		$post_type = $settings['post_type'];

		if ( $post_type === 'current_query' ) {
			return $GLOBALS['wp_query'];
		}

		// Loop query.
		$query_args = [
			'posts_offset'   => $settings['posts_offset'],
			'post_type'      => $post_type,
			'order'          => $settings['order'],
			'orderby'        => $settings['orderby'],
			'posts_per_page' => $settings['dis_posts_total'],
		];

		if ( $post_type === 'related' ) {
			$query_builder = new The7_Related_Query_Builder( $query_args );
		} else {
			$query_builder = new The7_Query_Builder( $query_args );
		}

		$query_builder->from_terms( $settings['taxonomy'], $settings['terms'] );

		if ( $request && $request->taxonomy && $request->not_empty() ) {
			$query_builder->with_categorizaition( $request );
		}

		return $query_builder->query();
	}

	/**
	 * @param string $element Element name.
	 *
	 * @return void
	 */
	protected function add_container_data_render_attributes( $element ) {
		$settings = $this->get_settings_for_display();

		$data_atts = [
			'data-scroll-mode'          => $settings['slide_to_scroll'] === 'all' ? 'page' : '1',
			'data-col-num'              => $settings['widget_columns'],
			'data-wide-col-num'         => $settings['widget_columns_wide_desktop'] ?: $settings['widget_columns'],
			'data-laptop-col'           => $settings['widget_columns_tablet'],
			'data-h-tablet-columns-num' => $settings['widget_columns_tablet'],
			'data-v-tablet-columns-num' => $settings['widget_columns_tablet'],
			'data-phone-columns-num'    => $settings['widget_columns_mobile'],
			'data-auto-height'          => $settings['adaptive_height'] ? 'true' : 'false',
			'data-col-gap'              => $settings['gap_between_posts']['size'] ?? null,
			'data-col-gap-tablet'       => $settings['gap_between_posts_tablet']['size'] ?? null,
			'data-col-gap-mobile'       => $settings['gap_between_posts_mobile']['size'] ?? null,
			'data-speed'                => $settings['speed'],
			'data-autoplay'             => $settings['autoplay'] ? 'true' : 'false',
			'data-autoplay_speed'       => $settings['autoplay_speed'],
		];

		$this->add_render_attribute( $element, $data_atts );
	}

	/**
	 * Return shortcode less file absolute path to output inline.
	 *
	 * @return string
	 */
	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-elements-carousel-widget.less';
	}

	/**
	 * Return less imports.
	 *
	 * @return array
	 */
	protected function get_less_imports() {
		$settings           = $this->get_settings_for_display();
		$dynamic_import_top = [];

		switch ( $settings['post_layout'] ) {
			case 'bottom_overlap':
				$dynamic_import_top[] = 'post-layouts/bottom-overlap-layout.less';
				break;
			case 'gradient_overlap':
				$dynamic_import_top[] = 'post-layouts/gradient-overlap-layout.less';
				break;
			case 'gradient_overlay':
				$dynamic_import_top[] = 'post-layouts/gradient-overlay-layout.less';
				break;
			case 'gradient_rollover':
				$dynamic_import_top[] = 'post-layouts/content-rollover-layout.less';
				break;
			case 'classic':
			default:
				$dynamic_import_top[] = 'post-layouts/classic-layout.less';
		}

		$dynamic_import_bottom = [];

		return compact( 'dynamic_import_top', 'dynamic_import_bottom' );
	}

	/**
	 * @param  The7_Elementor_Less_Vars_Decorator_Interface $less_vars Less vars manager object.
	 *
	 * @return void
	 */
	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		$settings = $this->get_settings_for_display();

		$less_vars->add_keyword(
			'unique-shortcode-class-name',
			$this->get_unique_class() . '.portfolio-shortcode',
			'~"%s"'
		);
	}

	/**
	 * @return void
	 */
	protected function add_query_content_controls() {
		/**
		 * Must have section_id = query_section to work properly.
		 *
		 * @see elements-widget-settings.js:onEditSettings()
		 */
		$this->start_controls_section(
			'query_section',
			[
				'label' => esc_html__( 'Query', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'post_type',
			[
				'label'   => esc_html__( 'Source', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => 'post',
				'options' => the7_elementor_elements_widget_post_types() + [ 'related' => esc_html__( 'Related', 'the7mk2' ) ],
				'classes' => 'select2-medium-width',
			]
		);

		$this->add_control(
			'taxonomy',
			[
				'label'     => esc_html__( 'Select Taxonomy', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => [],
				'classes'   => 'select2-medium-width',
				'condition' => [
					'post_type!' => [ '', 'current_query' ],
				],
			]
		);

		$this->add_control(
			'terms',
			[
				'label'     => esc_html__( 'Select Terms', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT2,
				'default'   => '',
				'multiple'  => true,
				'options'   => [],
				'classes'   => 'select2-medium-width',
				'condition' => [
					'taxonomy!'  => '',
					'post_type!' => [ 'current_query', 'related' ],
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'     => esc_html__( 'Order', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'desc',
				'options'   => [
					'asc'  => esc_html__( 'Ascending', 'the7mk2' ),
					'desc' => esc_html__( 'Descending', 'the7mk2' ),
				],
				'condition' => [
					'post_type!' => 'current_query',
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'     => esc_html__( 'Order By', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => [
					'date'          => esc_html__( 'Date', 'the7mk2' ),
					'title'         => esc_html__( 'Name', 'the7mk2' ),
					'ID'            => esc_html__( 'ID', 'the7mk2' ),
					'modified'      => esc_html__( 'Modified', 'the7mk2' ),
					'comment_count' => esc_html__( 'Comment count', 'the7mk2' ),
					'menu_order'    => esc_html__( 'Menu order', 'the7mk2' ),
					'rand'          => esc_html__( 'Rand', 'the7mk2' ),
				],
				'condition' => [
					'post_type!' => 'current_query',
				],
			]
		);

		$this->add_control(
			'dis_posts_total',
			[
				'label'       => esc_html__( 'Total Number Of Posts', 'the7mk2' ),
				'description' => esc_html__( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '12',
				'condition'   => [
					'post_type!' => 'current_query',
				],
			]
		);

		$this->add_control(
			'posts_offset',
			[
				'label'       => esc_html__( 'Posts Offset', 'the7mk2' ),
				'description' => esc_html__(
					'Offset for posts query (i.e. 2 means, posts will be displayed starting from the third post).',
					'the7mk2'
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'condition'   => [
					'post_type!' => 'current_query',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_content_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'article_links',
			[
				'label'        => esc_html__( 'Links To A Single Post', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
			]
		);

		$this->add_control(
			'article_links_goes_to',
			[
				'label'     => esc_html__( 'Links Lead To', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'posts',
				'options'   => [
					'posts'                => esc_html__( 'Posts', 'the7mk2' ),
					'external_or_posts'    => esc_html__( 'External links or posts', 'the7mk2' ),
					'external_or_disabled' => esc_html__( 'External links or disabled', 'the7mk2' ),
				],
				'condition' => [
					'article_links' => 'y',
				],
			]
		);

		$this->add_control(
			'article_link_meta_field',
			[
				'label'       => esc_html__( 'Link Meta Field', 'the7mk2' ),
				'description' => esc_html__( 'Post meta field name, f.e. site_link, with url.', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'meta_field_name',
				'condition'   => [
					'post_type!'             => 'dt_portfolio',
					'article_links_goes_to!' => 'posts',
					'article_links'          => 'y',
				],
			]
		);

		$this->add_control(
			'show_details_icon',
			[
				'label'        => esc_html__( 'Hover Icon', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'project_link_icon',
			[
				'label'     => esc_html__( 'Choose Icon', 'the7mk2' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-plus',
					'library' => 'fa-solid',
				],
				'condition' => [
					'show_details_icon' => 'y',
				],
			]
		);

		$this->add_control(
			'show_post_title',
			[
				'label'        => esc_html__( 'Title', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'     => esc_html__( 'Title HTML Tag', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default'   => 'h3',
				'condition' => [
					'show_post_title' => 'y',
				],
			]
		);

		$this->add_control(
			'post_content',
			[
				'label'        => esc_html__( 'Excerpt', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'show_excerpt',
				'default'      => 'show_excerpt',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'excerpt_words_limit',
			[
				'label'       => esc_html__( 'Maximum Number Of Words', 'the7mk2' ),
				'description' => esc_html__( 'Leave empty to show the entire excerpt.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'post_content' => 'show_excerpt',
				],
			]
		);

		$this->add_control(
			'post_terms',
			[
				'label'        => esc_html__( 'Category', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_terms_link',
			[
				'label'        => esc_html__( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_terms' => 'y',
				],
			]
		);

		$this->add_control(
			'post_author',
			[
				'label'        => esc_html__( 'Author', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_author_link',
			[
				'label'        => esc_html__( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_author' => 'y',
				],
			]
		);

		$this->add_control(
			'post_date',
			[
				'label'        => esc_html__( 'Date', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_date_link',
			[
				'label'        => esc_html__( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_date' => 'y',
					'post_type' => [ 'post', 'current_query', 'related' ],
				],
			]
		);

		$this->add_control(
			'post_comments',
			[
				'label'        => esc_html__( 'Comments count', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_comments_link',
			[
				'label'        => esc_html__( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_comments' => 'y',
				],
			]
		);

		$this->add_control(
			'show_read_more_button',
			[
				'label'        => esc_html__( 'Read More', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'read_more_button_text',
			[
				'label'     => esc_html__( 'Button Text', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Read more', 'the7mk2' ),
				'condition' => [
					'show_read_more_button' => 'y',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_layout_content_controls() {
		$this->start_controls_section(
			'layout_section',
			[
				'label' => esc_html__( 'Layout', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'widget_columns_wide_desktop',
			[
				'label'   => esc_html__( 'Columns On A Wide Desktop', 'the7mk2' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => '',
				'min'     => 1,
				'max'     => 12,
			]
		);

		$this->add_control(
			'widget_columns_wide_desktop_breakpoint',
			[
				'label'              => esc_html__( 'Wide Desktop Breakpoint (px)', 'the7mk2' ),
				'description'        => the7_elementor_get_wide_columns_control_description(),
				'type'               => Controls_Manager::NUMBER,
				'default'            => '',
				'min'                => 0,
				'frontend_available' => true,
			]
		);

		$this->add_basic_responsive_control(
			'widget_columns',
			[
				'label'          => esc_html__( 'Columns', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'min'            => 0,
				'max'            => 12,
			]
		);

		$this->template( Image_Size::class )->add_style_controls();

		$this->add_basic_responsive_control(
			'gap_between_posts',
			[
				'label'      => esc_html__( 'Gap Between Columns (px)', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 15,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'separator'  => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'carousel_margin',
			[
				'label'       => esc_html__( 'outer gaps', 'the7mk2' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .owl-stage, {{WRAPPER}} .owl-carousel' => '--stage-top-gap:{{TOP}}{{UNIT}}; --stage-right-gap:{{RIGHT}}{{UNIT}};  --stage-left-gap:{{LEFT}}{{UNIT}}; --stage-bottom-gap:{{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}} .owl-stage-outer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'adaptive_height',
			[
				'label'        => esc_html__( 'Adaptive Height', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_skin_style_controls() {
		$this->start_controls_section(
			'skins_style_section',
			[
				'label' => esc_html__( 'Skin', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'post_layout',
			[
				'label'   => esc_html__( 'Choose Skin', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'classic',
				'options' => [
					'classic'           => esc_html__( 'Classic', 'the7mk2' ),
					'bottom_overlap'    => esc_html__( 'Overlapping content area', 'the7mk2' ),
					'gradient_overlap'  => esc_html__( 'Blurred content area', 'the7mk2' ),
					'gradient_overlay'  => esc_html__( 'Simple overlay on hover', 'the7mk2' ),
					'gradient_rollover' => esc_html__( 'Blurred bottom overlay on hover', 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'classic_image_visibility',
			[
				'label'        => esc_html__( 'Image Visibility', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the7mk2' ),
				'label_off'    => esc_html__( 'Hide', 'the7mk2' ),
				'return_value' => 'show',
				'default'      => 'show',
				'condition'    => [
					'post_layout' => 'classic',
				],
			]
		);

		$this->add_basic_responsive_control(
			'classic_image_max_width',
			[
				'label'      => esc_html__( 'Max Image Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
					'size' => '',
				],
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 2000,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .classic-layout-list .post-thumbnail' => 'max-width: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'post_layout'              => 'classic',
					'classic_image_visibility' => 'show',
				],
			]
		);

		$this->add_basic_responsive_control(
			'bo_content_overlap',
			[
				'label'      => esc_html__( 'Content Box Overlap', 'the7mk2' ) . ' (px)',
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bottom-overlap-layout-list article:not(.no-img) .post-entry-content'      => 'margin-top: -{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'post_layout' => 'bottom_overlap',
				],
			]
		);

		$this->add_control(
			'go_animation',
			[
				'label'     => esc_html__( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'fade'              => esc_html__( 'Fade', 'the7mk2' ),
					'direction_aware'   => esc_html__( 'Direction aware', 'the7mk2' ),
					'redirection_aware' => esc_html__( 'Reverse direction aware', 'the7mk2' ),
					'scale_in'          => esc_html__( 'Scale in', 'the7mk2' ),
				],
				'condition' => [
					'post_layout' => 'gradient_overlay',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_box_style_controls() {
		$this->start_controls_section(
			'box_section',
			[
				'label' => esc_html__( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'box_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} article' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} article' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'box_padding',
			[
				'label'      => esc_html__( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .owl-carousel' => '--box-padding-top: {{TOP}}{{UNIT}}; --box-padding-bottom: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}} article'       => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'box_style_tabs' );

		$this->start_controls_tab(
			'classic_style_normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'box_shadow',
				'selector'       => '{{WRAPPER}} .the7-elementor-widget:not(.class-1) article',
				'fields_options' => [
					'box_shadow' => [
						'selectors' => [
							'{{SELECTOR}}'                 => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
							'{{WRAPPER}} .owl-stage-outer' => '--shadow-horizontal: {{HORIZONTAL}}px; --shadow-vertical: {{VERTICAL}}px; --shadow-blur: {{BLUR}}px; --shadow-spread: {{SPREAD}}px',
						],
					],
				],
			]
		);

		$this->add_control(
			'box_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} article' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} article' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'classic_style_hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow_hover',
				'selector' => '{{WRAPPER}} .the7-elementor-widget:not(.class-1) article:hover',
			]
		);

		$this->add_control(
			'box_background_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} article:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} article:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_hover_icon_style_controls() {
		$this->start_controls_section(
			'icon_style_section',
			[
				'label'      => esc_html__( 'Hover Icon', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'post_layout',
									'operator' => '!==',
									'value'    => 'classic',
								],
								[
									'name'     => 'show_details_icon',
									'operator' => '==',
									'value'    => 'y',
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'post_layout',
									'operator' => '==',
									'value'    => 'classic',
								],
								[
									'name'     => 'show_details_icon',
									'operator' => '==',
									'value'    => 'y',
								],
								[
									'name'     => 'classic_image_visibility',
									'operator' => '==',
									'value'    => 'show',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'project_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-hover-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .the7-hover-icon > svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'project_icon_bg_size',
			[
				'label'      => esc_html__( 'Background Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-hover-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'project_icon_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-hover-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid;',
				],
			]
		);

		$this->add_control(
			'project_icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-hover-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'project_icon_margin',
			[
				'label'      => esc_html__( 'Icon Margin', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .the7-hover-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'icon_style_tabs' );

		$this->start_controls_tab(
			'icons_colors',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'project_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-hover-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .the7-hover-icon > svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'project_icon_border_color',
			[
				'label'     => esc_html__( 'Icon Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-hover-icon' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'project_icon_bg_color',
			[
				'label'     => esc_html__( 'Icon Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-hover-icon' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icons_hover_colors',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'project_icon_color_hover',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-hover-icon:hover'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .the7-hover-icon:hover > svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'project_icon_border_color_hover',
			[
				'label'     => esc_html__( 'Icon Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-hover-icon:hover' => 'border-color: {{VALUE}};',

				],
			]
		);

		$this->add_control(
			'project_icon_bg_color_hover',
			[
				'label'     => esc_html__( 'Icon Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-hover-icon:hover' => 'background: {{VALUE}}; box-shadow: none;',

				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_image_style_controls() {
		$this->start_controls_section(
			'section_design_image',
			[
				'label'      => esc_html__( 'Image', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'post_layout',
							'operator' => '!==',
							'value'    => 'classic',
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'post_layout',
									'operator' => '==',
									'value'    => 'classic',
								],
								[
									'name'     => 'classic_image_visibility',
									'operator' => '==',
									'value'    => 'show',
								],
							],
						],
					],
				],
			]
		);

		$this->template( Image_Aspect_Ratio::class )->add_style_controls();

		$this->add_control(
			'img_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .post-thumbnail-wrap .post-thumbnail'         => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .post-thumbnail-wrap .post-thumbnail > .post-thumbnail-rollover'     => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .post-thumbnail-wrap .post-thumbnail > .post-thumbnail-rollover img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_scale_animation_on_hover',
			[
				'label'   => esc_html__( 'Scale Animation On Hover', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'quick_scale',
				'options' => [
					'disabled'    => esc_html__( 'Disabled', 'the7mk2' ),
					'quick_scale' => esc_html__( 'Quick scale', 'the7mk2' ),
					'slow_scale'  => esc_html__( 'Slow scale', 'the7mk2' ),
				],
			]
		);

		$this->start_controls_tabs( 'thumbnail_effects_tabs' );

		$this->start_controls_tab(
			'normal',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Overlay', 'the7mk2' ),
					],
				],
				'selector'       => '
				{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail > .post-thumbnail-rollover:before,
				{{WRAPPER}} .description-on-hover article .post-thumbnail > .post-thumbnail-rollover:before,
				{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail > .post-thumbnail-rollover:after,
				{{WRAPPER}} .description-on-hover article .post-thumbnail > .post-thumbnail-rollover:after
				',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'img_shadow',
				'selector'  => '
				{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail,
				{{WRAPPER}} .description-on-hover article .post-thumbnail
				',
				'condition' => [
					'post_layout!' => [ 'gradient_rollover', 'gradient_overlay' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_filters',
				'selector' => '
				{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail img,
				{{WRAPPER}} .description-on-hover article .post-thumbnail img
				',
			]
		);

		$this->add_control(
			'thumbnail_opacity',
			[
				'label'      => esc_html__( 'Opacity', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail img,
					{{WRAPPER}} .description-on-hover article .post-thumbnail img' => 'opacity: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_hover_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Overlay', 'the7mk2' ),
					],
					'color'      => [
						'selectors' => [
							'{{SELECTOR}} { transition: all 0.3s; }
							{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail > .post-thumbnail-rollover:before,
							{{WRAPPER}} .gradient-overlap-layout-list article .post-thumbnail > .post-thumbnail-rollover:before,
							{{WRAPPER}} .description-on-hover article .post-thumbnail > .post-thumbnail-rollover:before { transition: opacity 0.3s;}
							{{WRAPPER}} .post-thumbnail:hover > .post-thumbnail-rollover:before,
							{{WRAPPER}} .post-thumbnail:not(:hover) > .post-thumbnail-rollover:after {transition-delay: 0.15s;}
							{{SELECTOR}}' => 'background: {{VALUE}};',
						],

					],
				],
				'selector'       => '
					{{WRAPPER}} .description-under-image .post-thumbnail-wrap .post-thumbnail > .post-thumbnail-rollover:after,
					{{WRAPPER}} .gradient-overlap-layout-list article .post-thumbnail > .post-thumbnail-rollover:after,
					{{WRAPPER}} .description-on-hover article .post-thumbnail > .post-thumbnail-rollover:after
				',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'img_hover_shadow',
				'selector'  => '
				{{WRAPPER}} .description-under-image .post-thumbnail-wrap:hover .post-thumbnail,
				{{WRAPPER}} .description-on-hover article:hover .post-thumbnail
				',
				'condition' => [
					'post_layout!' => [ 'gradient_rollover', 'gradient_overlay' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_hover_filters',
				'selector' => '
				{{WRAPPER}} .description-under-image .post-thumbnail-wrap:hover .post-thumbnail img,
				{{WRAPPER}} .description-on-hover article:hover .post-thumbnail img
				',
			]
		);

		$this->add_control(
			'thumbnail_hover_opacity',
			[
				'label'      => esc_html__( 'Opacity', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .description-under-image .post-thumbnail-wrap:hover .post-thumbnail img,
					{{WRAPPER}} .description-on-hover article:hover .post-thumbnail img' => 'opacity: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'img_bottom_margin',
			[
				'label'      => esc_html__( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .post-thumbnail-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'post_layout' => 'classic',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_content_style_controls() {
		$this->start_controls_section(
			'content_style_section',
			[
				'label' => esc_html__( 'Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'custom_content_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}}' => '--content-bg-color:{{VALUE}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'bo_content_width',
			[
				'label'      => esc_html__( 'Content Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
					'size' => '',
				],
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .description-under-image .post-entry-content'               => 'max-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .description-on-hover .post-entry-content .post-entry-body' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'post_content_padding',
			[
				'label'      => esc_html__( 'Content Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} article .post-entry-content'                       => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .content-rollover-layout-list .post-entry-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'post_content_box_alignment',
			[
				'label'                => esc_html__( 'Horizontal Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'toggle'               => false,
				'default'              => 'left',
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
				'selectors'            => [
					'{{WRAPPER}} .description-under-image .post-entry-content'                       => 'align-self: {{VALUE}};',
					'{{WRAPPER}} .description-on-hover .post-entry-content .post-entry-body'         => 'align-self: {{VALUE}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'post_content_alignment',
			[
				'label'     => esc_html__( 'Text Alignment', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'default'   => 'left',
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .post-entry-content' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .classic-layout-list .post-thumbnail-wrap' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_post_title_style_controls() {
		$this->start_controls_section(
			'post_title_style_section',
			[
				'label'     => esc_html__( 'Post Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_post_title' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'post_title_typography',
				'label'          => esc_html__( 'Typography', 'the7mk2' ),
				'selector'       => '{{WRAPPER}} .ele-entry-title',
				'fields_options' => [
					'font_family' => [
						'default' => '',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
					'font_weight' => [
						'default' => '',
					],
					'line_height' => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
				],
			]
		);

		$this->start_controls_tabs( 'post_title_style_tabs' );

		$this->start_controls_tab(
			'post_title_normal_style',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'custom_title_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'#the7-body {{WRAPPER}} article:not(.class-1):not(.keep-custom-css) .ele-entry-title a'       => 'color: {{VALUE}}',
					'#the7-body {{WRAPPER}} article:not(.class-1):not(.keep-custom-css) .ele-entry-title span'    => 'color: {{VALUE}}',
					'#the7-body {{WRAPPER}} article:not(.class-1):not(.keep-custom-css) .ele-entry-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'post_title_hover_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'post_title_color_hover',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'#the7-body {{WRAPPER}} article:not(.class-1):not(.keep-custom-css) .ele-entry-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'post_title_bottom_margin',
			[
				'label'      => esc_html__( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ele-entry-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .content-rollover-layout-list.meta-info-off .post-entry-wrapper' => 'bottom: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_post_meta_style_controls() {
		$this->start_controls_section(
			'post_meta_style_section',
			[
				'label'      => esc_html__( 'Meta Information', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'post_date',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'post_terms',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'post_author',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'post_comments',
							'operator' => '!==',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'post_meta_separator',
			[
				'label'       => esc_html__( 'Separator Between', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '•',
				'placeholder' => '',
				'selectors'   => [
					'{{WRAPPER}} .entry-meta .meta-item:not(:first-child):before' => 'content: "{{VALUE}}";',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'post_meta_typography',
				'label'          => esc_html__( 'Typography', 'the7mk2' ),
				'fields_options' => [
					'font_family' => [
						'default' => '',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
					'font_weight' => [
						'default' => '',
					],
					'line_height' => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .entry-meta, {{WRAPPER}} .entry-meta > span',
			]
		);

		$this->add_control(
			'post_meta_font_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .entry-meta > a, {{WRAPPER}} .entry-meta > span'             => 'color: {{VALUE}}',
					'{{WRAPPER}} .entry-meta > a:after, {{WRAPPER}} .entry-meta > span:after' => 'background: {{VALUE}}; -webkit-box-shadow: none; box-shadow: none;',
				],
			]
		);

		$this->add_control(
			'post_meta_bottom_margin',
			[
				'label'      => esc_html__( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .entry-meta' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .content-rollover-layout-list .post-entry-wrapper' => 'bottom: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_text_style_controls() {
		$this->start_controls_section(
			'post_text_style_section',
			[
				'label'     => esc_html__( 'Text', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'post_content' => 'show_excerpt',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'post_content_typography',
				'label'          => esc_html__( 'Typography', 'the7mk2' ),
				'fields_options' => [
					'font_family' => [
						'default' => '',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
					'font_weight' => [
						'default' => '',
					],
					'line_height' => [
						'default' => [
							'unit' => 'px',
							'size' => '',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .entry-excerpt *',
			]
		);

		$this->add_control(
			'post_content_color',
			[
				'label'     => esc_html__( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .entry-excerpt' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'post_content_bottom_margin',
			[
				'label'      => esc_html__( 'Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .entry-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_scrolling_controls() {
		$this->start_controls_section(
			'scrolling_section',
			[
				'label' => esc_html__( 'Scrolling', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'slide_to_scroll',
			[
				'label'   => esc_html__( 'Scroll Mode', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'single',
				'options' => [
					'single' => 'One slide at a time',
					'all'    => 'All slides',
				],
			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => esc_html__( 'Transition Speed (ms)', 'the7mk2' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => '600',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Autoplay Slides', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Autoplay Speed (ms)', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6000,
				'min'       => 100,
				'max'       => 10000,
				'step'      => 10,
				'condition' => [
					'autoplay' => 'y',
				],
			]
		);

		$this->end_controls_section();
	}
}
