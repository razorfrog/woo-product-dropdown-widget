<?php

/**
 * Plugin Name:       Product Dropdown Widget for WooCommerce
 * Plugin URI:        https://razorfrog.com/plugin-release-woocommerce-dropdown-widget/
 * Description:       Display WooCommerce products by category in a dropdown menu widget.
 * Version:           1.1.4
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Razorfrog Web Design
 * Author URI:        https://razorfrog.com/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       woo-product-dropdown-widget
 */

defined( 'ABSPATH' ) || exit;

class rz_woo_category_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'rz_woo_category_widget', // Base ID
			__( 'WooCommerce Product Dropdown', 'woo-product-dropdown-widget' ), // Name
			array(
				'description' => __( 'Display products from a specific product category as dropdown.', 'woo-product-dropdown-widget' ),
			)
		);
	}

	////////////////////////////////////////////////////////////////////////////////
	// Widget frontend
	////////////////////////////////////////////////////////////////////////////////

	public function widget( $args, $instance ) {

		extract( $args );
		echo wp_kses_post( $before_widget );

		$title        = isset( $instance['title'] ) ? $instance['title'] : '';
		$show_posts   = ( isset( $instance['show_posts'] ) && is_numeric( $instance['show_posts'] ) ) ? $instance['show_posts'] : -1;
		$sort         = isset( $instance['sort'] ) ? $instance['sort'] : '';
		$order        = isset( $instance['order'] ) ? $instance['order'] : '';
		$cat_dropdown = isset( $instance['cat_dropdown'] ) ? (int) $instance['cat_dropdown'] : 0;

		$args = array( // defaults to all categories
			'post_type'   => 'product',
			'orderby'     => $sort,
			'order'       => $order,
			'numberposts' => $show_posts,
		);

		$product_cat = 'Products';

		if ( 0 !== $cat_dropdown ) { // single product category
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'id',
					'terms'    => $cat_dropdown,
				),
			);
			$term        = get_term( $cat_dropdown, 'product_cat' );
			$product_cat = ( $term && ! is_wp_error( $term ) ) ? $term->name : 'Products';
		}

		if ( 'price' === $sort ) { // order by price
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_price';
		} elseif ( 'sales' === $sort ) { // order by total sales
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = 'total_sales';
		}

		$posts = get_posts( $args );

		if ( $posts ) {
			echo '<div class="' . esc_attr( $this->widget_options['classname'] ) . '-content">';

			if ( ! empty( $title ) ) {
				// Filter then escape the widget title.
				$filtered_title = apply_filters( 'widget_title', $title );
				echo wp_kses_post( $before_title ) . esc_html( $filtered_title ) . wp_kses_post( $after_title );
			}

			echo '<select class="category-posts-dropdown" aria-label="' . esc_attr( 'View ' . $product_cat ) . '" onchange="if (this.value) { location = this.value; }" style="width:100%">';
			echo '<option value="">' . esc_html( 'View ' . $product_cat ) . '</option>';

			foreach ( $posts as $post ) {

				$additional = '';

				// If sorted by reviews, show (count)
				if ( 'comment_count' === $sort && 1 === (int) $post->comment_count ) {
					$additional = ' (' . absint( $post->comment_count ) . ' ' . __( 'Review', 'woo-product-dropdown-widget' ) . ')';
				} elseif ( 'comment_count' === $sort && (int) $post->comment_count > 1 ) {
					$additional = ' (' . absint( $post->comment_count ) . ' ' . __( 'Reviews', 'woo-product-dropdown-widget' ) . ')';
				}
				?>
				<option value="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
					<?php echo esc_html( $post->post_title . $additional ); ?>
				</option>
				<?php
			}

			echo '</select>';
			echo '</div>';
		}

		echo wp_kses_post( $after_widget );
	}

	////////////////////////////////////////////////////////////////////////////////
	// Widget backend - admin dashboard
	////////////////////////////////////////////////////////////////////////////////

	public function form( $instance ) {
		$defaults = array(
			'cat_dropdown' => '',
			'title'        => '',
			'sort'         => 'title',
			'order'        => 'ASC',
			'show_posts'   => -1,
		);

		$instance     = wp_parse_args( (array) $instance, $defaults );
		$cat_dropdown = isset( $instance['cat_dropdown'] ) ? $instance['cat_dropdown'] : '';

		$product_categories_dropdown = wp_dropdown_categories(
			array(
				'taxonomy'        => 'product_cat',
				'orderby'         => 'slug',
				'hierarchical'    => true,
				'echo'            => false,
				'show_option_all' => __( 'All Products', 'woo-product-dropdown-widget' ),
				'show_count'      => true,
				'selected'        => $cat_dropdown,
				'name'            => $this->get_field_name( 'cat_dropdown' ),
				'id'              => $this->get_field_id( 'cat_dropdown' ),
				'class'           => 'widefat',
			)
		);
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'woo-product-dropdown-widget' ); ?>
			</label>
			<input type="text"
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'cat_dropdown' ) ); ?>">
				<?php esc_html_e( 'Category:', 'woo-product-dropdown-widget' ); ?>
			</label>
			<?php
			// Markup is generated by wp_dropdown_categories() and is safe in the admin.
			echo $product_categories_dropdown; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sort' ) ); ?>">
				<?php esc_html_e( 'Sort by:', 'woo-product-dropdown-widget' ); ?>
			</label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'sort' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort' ) ); ?>">
				<option value="title" <?php selected( $instance['sort'], 'title' ); ?>>
					<?php esc_html_e( 'Product Name', 'woo-product-dropdown-widget' ); ?>
				</option>
				<option value="menu_order" <?php selected( $instance['sort'], 'menu_order' ); ?>>
					<?php esc_html_e( 'Menu Order', 'woo-product-dropdown-widget' ); ?>
				</option>
				<option value="price" <?php selected( $instance['sort'], 'price' ); ?>>
					<?php esc_html_e( 'Price', 'woo-product-dropdown-widget' ); ?>
				</option>
				<option value="sales" <?php selected( $instance['sort'], 'sales' ); ?>>
					<?php esc_html_e( 'Total Sales', 'woo-product-dropdown-widget' ); ?>
				</option>
				<option value="date" <?php selected( $instance['sort'], 'date' ); ?>>
					<?php esc_html_e( 'Date Published', 'woo-product-dropdown-widget' ); ?>
				</option>
				<option value="modified" <?php selected( $instance['sort'], 'modified' ); ?>>
					<?php esc_html_e( 'Date Last Modified', 'woo-product-dropdown-widget' ); ?>
				</option>
				<option value="comment_count" <?php selected( $instance['sort'], 'comment_count' ); ?>>
					<?php esc_html_e( 'Reviews', 'woo-product-dropdown-widget' ); ?>
				</option>
				<option value="rand" <?php selected( $instance['sort'], 'rand' ); ?>>
					<?php esc_html_e( 'Random', 'woo-product-dropdown-widget' ); ?>
				</option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
				<?php esc_html_e( 'Sort Order:', 'woo-product-dropdown-widget' ); ?>
			</label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
				<option value="ASC" <?php selected( $instance['order'], 'ASC' ); ?>>
					<?php esc_html_e( 'Ascending (A > Z)', 'woo-product-dropdown-widget' ); ?>
				</option>
				<option value="DESC" <?php selected( $instance['order'], 'DESC' ); ?>>
					<?php esc_html_e( 'Descending (Z > A)', 'woo-product-dropdown-widget' ); ?>
				</option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_posts' ) ); ?>">
				<?php esc_html_e( 'Number of Products (Defaults to All):', 'woo-product-dropdown-widget' ); ?>
			</label>
			<input type="text"
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'show_posts' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'show_posts' ) ); ?>"
				value="<?php echo esc_attr( $instance['show_posts'] ); ?>" />
		</p>
		<?php
	}

	////////////////////////////////////////////////////////////////////////////////
	// Sanitize widget form values as they are saved
	////////////////////////////////////////////////////////////////////////////////

	public function update( $new_instance, $old_instance ) {
		$instance                 = array();
		$instance['title']        = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['show_posts']   = isset( $new_instance['show_posts'] ) ? sanitize_text_field( $new_instance['show_posts'] ) : '';
		$instance['sort']         = isset( $new_instance['sort'] ) ? sanitize_text_field( $new_instance['sort'] ) : '';
		$instance['order']        = isset( $new_instance['order'] ) ? sanitize_text_field( $new_instance['order'] ) : '';
		$instance['cat_dropdown'] = isset( $new_instance['cat_dropdown'] ) ? (int) $new_instance['cat_dropdown'] : 0;
		return $instance;
	}

}

add_action( 'widgets_init', 'register_rz_woo_dropdown_widget' );
function register_rz_woo_dropdown_widget() {
	register_widget( 'rz_woo_category_widget' );
}

?>
