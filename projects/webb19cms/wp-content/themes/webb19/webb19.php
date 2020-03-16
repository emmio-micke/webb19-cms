<?php
/*
 * Plugin Name: Nice Plugin
 * Description: Does something nice.
 * Author:      Mikael Olsson
 * Author URI:  https://author.example.com/
 * Text Domain: my-basics-plugin
 */

function register_webb19_menu_page() {
    add_menu_page('Webb19 Page', 'Webb19', 'manage_options', 'webb19');
}

// add_action('admin_menu', 'register_webb19_menu_page');


function webb19_filter_title($title) {
    return 'Webb19: ' . $title . ' hello';
}

// add_filter( 'the_title', 'webb19_filter_title' );


function change_woocommerce_products_per_page() {
    return 5;
}

add_filter('loop_shop_per_page', 'change_woocommerce_products_per_page', 20);

function add_sold_badge_mark() {
    global $product;
    
    if (! $product->is_in_stock()) {
        echo '<span class="out-of-stock" style="position: absolute; top: 0; left: 0; text-align: center; width: 100%;">Sold out</span>';
    }
}

add_action('woocommerce_before_shop_loop_item_title', 'add_sold_badge_mark');



function print_hello_table( $args ) {
    // Get data from database or API.
    $data = [
        [
            'name' => 'Kalle',
            'age' => 44
        ],
        [
            'name' => 'Linda',
            'age' => 43
        ],
        [
            'name' => 'Gustav',
            'age' => 13
        ]
    ];

	$output = '
        <table class="table">
            <tr class="header">
                <th>Name</th>
                <th>Age</th>
            </tr>';

	foreach ( $data as $person ) {
		$output .= "
            <tr>
                <td>{$person['name']}</td>
                <td>{$person['age']}</td>
            </tr>";
	}

	$output .= '</table>';

	return $output;
}

add_shortcode( 'webb19-hello', 'print_hello_table' );

/**
 * Adds Foo_Widget widget.
 */
class Foo_Widget extends WP_Widget {
 
    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'foo_widget', // Base ID
            'Foo_Widget', // Name
            array( 'description' => __( 'A Foo Widget', 'text_domain' ), ) // Args
        );
    }
 
    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );
 
        echo $before_widget;
        if ( ! empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }
        echo __( 'Hello, World!', 'text_domain' );
        echo $after_widget;
    }
 
    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'text_domain' );
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
         </p>
    <?php
    }
 
    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
 
        return $instance;
    }
 
} // class Foo_Widget
 
// Register Foo_Widget widget
add_action( 'widgets_init', 'register_foo' );
     
function register_foo() { 
    register_widget( 'Foo_Widget' ); 
}
