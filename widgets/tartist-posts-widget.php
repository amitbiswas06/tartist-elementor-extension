<?php
/**
 * Elementor Posts Widget.
 *
 * Elementor widget that inserts posts with different settings and controls.
 *
 * @since 1.0.0
 */
class Tartist_Elementor_Posts_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'tartist_posts';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Posts Grid', 'tartist-elementor-extension' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fas fa-plug';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'tartist-widgets' ];
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		/**
		 * Content Settings
		 */
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'tartist-elementor-extension' ),
				'tab' 	=> \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'posts_return_type',
			[
				'label' 		=> __( 'Show posts by', 'tartist-elementor-extension' ),
				'label_block' 	=> true,
				'type' 			=> \Elementor\Controls_Manager::SELECT,
				'default' 		=> 'recent_posts',
				'options' 		=> [
					'recent_posts'  => __( 'Most Recent', 'tartist-elementor-extension' ),
					'by_category' 	=> __( 'By Category', 'tartist-elementor-extension' ),
					'manually' 		=> __( 'Manual Selection', 'tartist-elementor-extension' ),
				],
			]
		);

		$this->add_control(
			'total_posts',
			[
				'label' 		=> __( 'Total Posts', 'tartist-elementor-extension' ),
				'type' 			=> \Elementor\Controls_Manager::NUMBER,
				'min' 			=> 1,
				'max' 			=> 100,
				'step' 			=> 1,
				'default' 		=> 3,
				'condition'		=> [
					'posts_return_type!' => 'manually'
				],
			]
		);

		$this->add_control(
			'select_category',
			[
				'label' 		=> __( 'Select Category', 'tartist-elementor-extension' ),
				'label_block' 	=> true,
				'type' 			=> \Elementor\Controls_Manager::SELECT,
				'options' 		=> TartistElementeor__terms_array('category'),
				'default' 		=> '',
				'condition'		=> [
					'posts_return_type' => 'by_category'
				],
			]
		);

		$this->add_control(
			'select_posts',
			[
				'label' 		=> __( 'Select Posts', 'tartist-elementor-extension' ),
				'label_block' 	=> true,
				'type' 			=> \Elementor\Controls_Manager::SELECT2,
				'multiple' 		=> true,
				'options' 		=> TartistElementeor__posts_array('post'),
				'default' 		=> [],
				'condition'		=> [
					'posts_return_type' => 'manually'
				],
			]
		);

		$this->add_control(
			'remove_post_elements',
			[
				'label' 		=> __( 'Remove Post Elements', 'tartist-elementor-extension' ),
				'label_block' 	=> true,
				'type' 			=> \Elementor\Controls_Manager::SELECT2,
				'multiple' 		=> true,
				'options' 		=> array(
					'image'		=> esc_html__('Featured Image','tartist-elementor-extension'),
					'excerpt'	=> esc_html__('Excerpt','tartist-elementor-extension'),
				),
				'default' 		=> [],
			]
		);

		$this->end_controls_section();

		/**
		 * Layout Settings
		 */
		$this->start_controls_section(
			'layout_settings',
			[
				'label' => __( 'Layout Settings', 'tartist-elementor-extension' ),
				'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'cols_per_row',
			[
				'label' 	=> __( 'Columns per row', 'tartist-elementor-extension' ),
				'type' 		=> \Elementor\Controls_Manager::NUMBER,
				'min' 		=> 1,
				'max' 		=> 6,
				'step' 		=> 1,
				'default' 	=> 3,
				'selectors' => [
					'{{WRAPPER}} .tartist-posts-grid__wrap' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'space_between',
			[
				'label' 	=> __( 'Spacing (em)', 'tartist-elementor-extension' ),
				'type' 		=> \Elementor\Controls_Manager::NUMBER,
				'min' 		=> 1,
				'max' 		=> 6,
				'step' 		=> 1,
				'default' 	=> 1.5,
				'selectors' => [
					'{{WRAPPER}} .tartist-posts-grid__wrap' => 'grid-gap: {{VALUE}}em;',
				],
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label' 	=> __( 'Item Border Radius(px)', 'tartist-elementor-extension' ),
				'type' 		=> \Elementor\Controls_Manager::NUMBER,
				'min' 		=> 1,
				'max' 		=> 10,
				'step' 		=> 1,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .tartist-posts-grid__post' => 'border-radius: {{VALUE}}px;',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Color Settings
		 */
		$this->start_controls_section(
			'color_settings',
			[
				'label' => __( 'Color Settings', 'tartist-elementor-extension' ),
				'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'bg_color',
			[
				'label' 	=> __( 'Background Color', 'tartist-elementor-extension' ),
				'type' 		=> \Elementor\Controls_Manager::COLOR,
				'scheme' 	=> [
					'type' 	=> \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .tartist-posts-grid__post' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' 	=> __( 'Text Color', 'tartist-elementor-extension' ),
				'type' 		=> \Elementor\Controls_Manager::COLOR,
				'scheme' 	=> [
					'type' 	=> \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .tartist-posts-grid__post' 	=> 'color: {{VALUE}};',
					'{{WRAPPER}} .tartist-posts-grid__post h3 a' 	=> 'color: {{VALUE}};',
					'{{WRAPPER}} .tartist-posts-grid__post .tartist-widget-btn'	=> 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		//settings
		$settings = $this->get_settings_for_display();
		//[content_section]:
			//['posts_return_type'] (select)
				//recent_posts, by_category, manually
			
			//['total_posts'] (number)
			//['select_category'] (select) retruns term_id
			//['select_posts'] (select2 multi) returns post ids in array

			$get_by = $settings['posts_return_type'];
			$total_posts = $settings['total_posts'];
			$category = $settings['select_category'];
			$manual_posts = $settings['select_posts'];
			$remove_post_elements = $settings['remove_post_elements'];

		?>

<div class="tartist-widget tartist-posts-grid">
    <div class="tartist-posts-grid__wrap">    
        
		<?php
			$args = array(
				'post_type'             => 'post',
				'ignore_sticky_posts'   => true,
				'post__not_in'          => array( get_the_ID() ),
				'post_status'           => 'publish'
			);  

			switch ( $get_by ) {

				//recent posts
				case 'recent_posts' : 
				$args['posts_per_page'] = $total_posts ? $total_posts : -1;
				break;
			
				//by category
				case 'by_category' :
				$args['posts_per_page'] = $total_posts ? $total_posts : -1;
				$args['cat'] = $category;
				break;
			
				//manual select
				case 'manually' :
				$args['post__in'] = $manual_posts;
				break;
			}
			
			$the_query = new WP_Query( $args );

			// The Loop
			if ( $the_query->have_posts() ) :
				while ( $the_query->have_posts() ) : $the_query->the_post();
		?>
		<div class="tartist-posts-grid__post">
			
			<?php 
				if( !in_array("image", $remove_post_elements ) ){
					if( has_post_thumbnail() ) { 
			?>
				<div class="post-thumbnail">
					<?php the_post_thumbnail('medium_large'); ?>
				</div>
			<?php }} ?>

			<div class="post-content">
				<div class="post-content__title">
					<?php
						printf(
							'<h3><a href="%s">%s</a></h3><span>%s</span>',
							esc_url( get_the_permalink() ),
							esc_html( get_the_title() ),
							esc_html( get_the_date() )
						);
					?>
				</div>
				<div class="post-content__content">
					<?php if( !in_array("excerpt", $remove_post_elements ) ) {?>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 25 ) ); ?></p>
					<?php } ?>
					<?php
						printf(
							'<a href="%s" class="tartist-widget-btn">%s</a>',
							esc_url(get_the_permalink()),
							esc_html__('Read More','tartist-elementor-extension')
						);
					?>
				</div>
			</div>
			
		</div>
		<?php 
			endwhile;
			
			else:
				echo esc_html__('Sorry, no item found in this criteria.','tartist-elementor-extension');
		endif; //endif

		// Reset Post Data
		wp_reset_postdata();
		?>

    </div>
</div>

		<?php
	}

}

?>