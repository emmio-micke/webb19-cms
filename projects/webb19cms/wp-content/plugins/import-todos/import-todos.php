<?php
/*
 * Plugin Name: Import Todos
 * Description: Imports todos from api and keep them updated.
 * Author:      Mikael Olsson
 * Author URI:  https://author.example.com/
 * Text Domain: import-todos
 */

function update_todo_items()
{
	// Get data from database or API.
	$api_url = 'https://jsonplaceholder.typicode.com/todos/';

	// Read JSON file
	//$json_data = file_get_contents($api_url);

	$json_data = '[
		{
		  "userId": 1,
		  "id": 1,
		  "title": "delectus aut autem",
		  "completed": false
		},
		{
		  "userId": 1,
		  "id": 2,
		  "title": "quis ut nam facilis et officia qui",
		  "completed": false
		},
		{
		  "userId": 1,
		  "id": 3,
		  "title": "fugiat veniam minus",
		  "completed": false
		},
		{
		  "userId": 1,
		  "id": 4,
		  "title": "et porro tempora",
		  "completed": true
		},
		{
		  "userId": 1,
		  "id": 5,
		  "title": "laboriosam mollitia et enim quasi adipisci quia provident illum",
		  "completed": false
		},
		{
		  "userId": 1,
		  "id": 6,
		  "title": "qui ullam ratione quibusdam voluptatem quia omnis",
		  "completed": false
		},
		{
		  "userId": 1,
		  "id": 7,
		  "title": "illo expedita consequatur quia in",
		  "completed": false
		},
		{
		  "userId": 1,
		  "id": 8,
		  "title": "quo adipisci enim quam ut ab",
		  "completed": true
		},
		{
		  "userId": 1,
		  "id": 9,
		  "title": "molestiae perspiciatis ipsa",
		  "completed": false
		},
		{
		  "userId": 1,
		  "id": 10,
		  "title": "illo est ratione doloremque quia maiores aut",
		  "completed": true
		}
	  ]';

	// Decode JSON data into PHP array
	$todo_data = json_decode($json_data, true);

	foreach ($todo_data as $todo_item) {
		$args = [
			'post_type' => 'todo',
			'meta_query' => [
				[
					'key' => 'remote_id',
					'value' => $todo_item['id']
				]
			]
		];

		$query = new WP_Query($args);
		$existing_post = false;

		if ($query->have_posts()) {
			$query->the_post();
			$existing_post = get_the_ID();

			echo "Will update post $existing_post<br>";

			$post_id = wp_update_post(
				[
					'ID'                => $existing_post,
					'post_title'        => $todo_item['title'],
				]
			);
		} else {
			echo "Will create a new post<br>";
			$post_id = wp_insert_post(
				[
					'comment_status'    => 'closed',
					'ping_status'       => 'closed',
					'post_author'       => 1,
					'post_title'        => $todo_item['title'],
					'post_status'       => 'publish',
					'post_type'         => 'todo'
				]
			);
		}

		update_post_meta($post_id, 'remote_id', $todo_item['id']);
		update_post_meta($post_id, 'remote_userid', $todo_item['userId']);
		update_post_meta($post_id, 'remote_completed', $todo_item['completed']);
	}
}

/**
 * Summary.
 *
 * Description.
 *
 * @since 1.0.0
 *
 * @param array $args {
 *     Optional. An array of arguments.
 *
 *     @type type $key Description. Default 'value'. Accepts 'value', 'value'.
 *                     (aligned with Description, if wraps to a new line)
 *     @type type $key Description.
 * }
 * @return string Returns an HTML string describing table and form with todo items.
 */
function print_todo_items($args)
{
	ob_start();

	$query_args = [
		'post_type' => 'todo',
	];

	$query  = new WP_Query($query_args);
	$output = '';

	if ($query->have_posts()) :
		while ($query->have_posts()) :
			$query->the_post();

			// Display post content.
			$output .= get_the_title() . '<br>';
		endwhile;
	endif;

	get_template_part('my_form_template');

	return ob_get_clean();
}

add_shortcode('todo-items', 'print_todo_items');

function import_todos_register_cpt()
{

	/**
	 * Post Type: Todos.
	 */

	$labels = [
		"name" => __("Todos", "import-todos"),
		"singular_name" => __("Todo", "import-todos"),
		"menu_name" => __("My Todos", "import-todos"),
		"all_items" => __("All Todos", "import-todos"),
		"add_new" => __("Add new", "import-todos"),
		"add_new_item" => __("Add new Todo", "import-todos"),
		"edit_item" => __("Edit Todo", "import-todos"),
		"new_item" => __("New Todo", "import-todos"),
		"view_item" => __("View Todo", "import-todos"),
		"view_items" => __("View Todos", "import-todos"),
		"search_items" => __("Search Todos", "import-todos"),
		"not_found" => __("No Todos found", "import-todos"),
		"not_found_in_trash" => __("No Todos found in trash", "import-todos"),
		"parent" => __("Parent Todo:", "import-todos"),
		"featured_image" => __("Featured image for this Todo", "import-todos"),
		"set_featured_image" => __("Set featured image for this Todo", "import-todos"),
		"remove_featured_image" => __("Remove featured image for this Todo", "import-todos"),
		"use_featured_image" => __("Use as featured image for this Todo", "import-todos"),
		"archives" => __("Todo archives", "import-todos"),
		"insert_into_item" => __("Insert into Todo", "import-todos"),
		"uploaded_to_this_item" => __("Upload to this Todo", "import-todos"),
		"filter_items_list" => __("Filter Todos list", "import-todos"),
		"items_list_navigation" => __("Todos list navigation", "import-todos"),
		"items_list" => __("Todos list", "import-todos"),
		"attributes" => __("Todos attributes", "import-todos"),
		"name_admin_bar" => __("Todo", "import-todos"),
		"item_published" => __("Todo published", "import-todos"),
		"item_published_privately" => __("Todo published privately.", "import-todos"),
		"item_reverted_to_draft" => __("Todo reverted to draft.", "import-todos"),
		"item_scheduled" => __("Todo scheduled", "import-todos"),
		"item_updated" => __("Todo updated.", "import-todos"),
		"parent_item_colon" => __("Parent Todo:", "import-todos"),
	];

	$args = [
		"label" => __("Todos", "import-todos"),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => ["slug" => "todo", "with_front" => true],
		"query_var" => true,
		"supports" => ["title", "editor", "thumbnail"],
	];

	register_post_type("todo", $args);
}

add_action('init', 'import_todos_register_cpt');

if (function_exists('acf_add_local_field_group')) :

	acf_add_local_field_group(array(
		'key' => 'group_5e4ba1e9b09b0',
		'title' => 'Todo',
		'fields' => array(
			array(
				'key' => 'field_5e4ba21b49828',
				'label' => 'Remote Completed',
				'name' => 'remote_completed',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'todo',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
	));

endif;

/*
function wporg_simple_role()
{
  add_role(
	'simple_role',
	'Simple Role',
	[
	  'read'         => true,
	  'edit_posts'   => true,
	  'upload_files' => true,
	]
  );
}

// Add the simple_role.
add_action('init', 'wporg_simple_role');
*/

/*
function wporg_simple_role_remove()
{
  remove_role('simple_role');
}

// Remove the simple_role.
add_action('init', 'wporg_simple_role_remove');
*/

/**
 * Generate a Delete link based on the homepage url.
 */
function wporg_generate_delete_link($content)
{
	// Run only for single post page.
	if (is_single() && in_the_loop() && is_main_query()) {
		// Add query arguments: action, post.
		$url = add_query_arg(
			[
				'action' => 'wporg_frontend_delete',
				'post'   => get_the_ID(),
			],
			home_url()
		);
		return $content . ' <a href="' . esc_url($url) . '">' . esc_html__('Delete Post', 'wporg') . '</a>';
	}
	return null;
}

/**
 * Request handler.
 */
function wporg_delete_post()
{
	if (isset($_GET['action']) && $_GET['action'] === 'wporg_frontend_delete') {

		// Verify we have a post id.
		$post_id = (isset($_GET['post'])) ? ($_GET['post']) : (null);

		// Verify there is a post with such a number.
		$post = get_post((int) $post_id);
		if (empty($post)) {
			return;
		}

		// Delete the post.
		wp_trash_post($post_id);

		// Redirect to admin page.
		$redirect = admin_url('edit.php');
		wp_safe_redirect($redirect);

		// We are done.
		die;
	}
}


add_action('plugins_loaded', 'check_current_user');

function check_current_user()
{
	$current_user = wp_get_current_user();

	if (current_user_can('edit_others_posts')) {
		/**
		 * Add the delete link to the end of the post content.
		 */
		add_filter('the_content', 'wporg_generate_delete_link');

		/**
		 * Register our request handler with the init hook.
		 */
		wporg_delete_post();
	}
}

function todo_options_page_html()
{
	// Check user capabilities.
	if (!current_user_can('manage_options')) {
		return;
	}

	// Check if the user have submitted the settings.
	// Wordpress will add the "settings-updated" $_GET parameter to the url
	if (isset($_GET['settings-updated'])) {
		// Add settings saved message with the class of "updated".
		add_settings_error('todo_messages', 'todo_message', __('Settings Saved', 'import-todos'), 'updated');
	}

	// Show error/update messages.
	settings_errors('todo_messages');
?>

	<div class="wrap">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
		<form action="options.php" method="post">
			<?php
			// Output security fields for the registered setting "todo".
			settings_fields('todo');

			// Output setting sections and their fields.
			// (Sections are registered for "todo", each field is registered to a specific section).
			do_settings_sections('todo');

			// Output save settings button.
			submit_button(__('Save Settings', 'import-todos'));
			?>
		</form>
	</div>
<?php
}

function todo_options_page()
{
	add_submenu_page(
		'edit.php?post_type=todo',
		'Todo Options',
		__('Settings'),
		'manage_options',
		'todo',
		'todo_options_page_html'
	);
}

add_action('admin_menu', 'todo_options_page');

/**
 * Custom option and settings
 */
function todo_settings_init()
{
	// Register a new setting for "todo" page.
	register_setting('todo', 'todo_options');

	// Register a new section in the "todo" page.
	add_settings_section(
		'todo_section_developers',
		__('Todo API URL', 'import-todos'),
		'todo_section_developers_cb',
		'todo'
	);

	// Register a new field in the "todo_section_developers" section, inside the "todo" page.
	add_settings_field(
		'todo_field_api', // as of WP 4.6 this value is used only internally
		// use $args' label_for to populate the id inside the callback
		__('API URL', 'import-todos'),
		'todo_field_api_cb',
		'todo',
		'todo_section_developers',
		[
			'label_for' => 'todo_field_api',
			'class' => '',
		]
	);
}

/**
 * Register our todo_settings_init to the admin_init action hook.
 */
add_action('admin_init', 'todo_settings_init');

function todo_section_developers_cb($args)
{
?>
	<p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('API settings', 'import-todos'); ?></p>
<?php
}

function todo_field_api_cb($args)
{
	// Get the value of the setting we've registered with register_setting().
	$options = get_option('todo_options');

	// Output the field.
?>
	<input id="<?php echo esc_attr($args['label_for']); ?>" name="todo_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo $options[$args['label_for']]; ?>">

	<p class="description">
		<?php esc_html_e('Enter the API URL you would like to use.', 'import-todos'); ?>
	</p>
<?php
}

function wporg_custom_box_html($post)
{
	$value = get_post_meta($post->ID, '_wporg_meta_key', true);
?>
	<label for="remote_completed">Completed</label>
	<input type="checkbox" name="chk_completed" id="remote_completed" value="hello" <?php checked($value, 1); ?>>
	<input type="hidden" name="hidden_completed" value="true">
	<?php
}

function wporg_add_custom_box()
{
	$screens = ['post', 'todo'];
	foreach ($screens as $screen) {
		add_meta_box(
			'wporg_box_id',                 // Unique ID
			__('Custom Meta Box Title'),  // Box title
			'wporg_custom_box_html',  		// Content callback, must be of type callable
			$screen                         // Post type
		);
	}
}

add_action('add_meta_boxes', 'wporg_add_custom_box');

function wporg_save_postdata($post_id)
{
	if (array_key_exists('hidden_completed', $_POST)) {
		$completed = array_key_exists('chk_completed', $_POST);

		update_post_meta(
			$post_id,
			'_wporg_meta_key',
			$completed
		);
	}
}

add_action('save_post', 'wporg_save_postdata');

// Admin dashboard widget

function wporg_add_dashboard_widgets()
{
	wp_add_dashboard_widget(
		'wporg_dashboard_widget',                          // Widget slug.
		esc_html__('Example Dashboard Widget', 'wporg'), // Title.
		'wporg_dashboard_widget_render'                    // Display function.
	);
}
add_action('wp_dashboard_setup', 'wporg_add_dashboard_widgets');

/**
 * Create the function to output the content of our Dashboard Widget.
 */
function wporg_dashboard_widget_render()
{
	// Display whatever you want to show.
	esc_html_e("Howdy! I'm a great Dashboard Widget.", 'wporg');
}


add_action('restrict_manage_posts', 'todo_filter_posts_by_complete');

function todo_filter_posts_by_complete()
{
	$type = $_GET['post_type'] ?? 'todo';

	// Only add filter to post type you want.
	if ('todo' == $type) {
		$values = array(
			'Gjord' => 'complete',
			'Ej klar' => 'incomplete',
		);
	?>
		<select name="status">
			<option value=""><?php _e('Alla statusar'); ?></option>
			<?php
			$current_v = $_GET['status'] ?? '';
			foreach ($values as $label => $value) {
				printf(
					'<option value="%s"%s>%s</option>',
					$value,
					$value == $current_v ? ' selected="selected"' : '',
					$label
				);
			}
			?>
		</select>
<?php
	}
}


add_filter( 'parse_query', 'todo_filter' );

function todo_filter( $query )
{
	global $pagenow;

	$type = $_GET['post_type'] ?? 'todo';
	$status = $_GET['status'] ?? '';

	if ('todo' == $type && is_admin() && $pagenow == 'edit.php' && $status != '') {
		$query->query_vars['meta_key'] = 'remote_completed';

		if ('complete' == $status) {
			$query->query_vars['meta_value'] = 1;
		}

		if ('incomplete' == $status) {
			$query->query_vars['meta_value'] = [''];
			$query->query_vars['meta_compare'] = 'EXISTS';
		}
	}
}

add_filter( 'manage_todo_posts_columns', 'custom_todo_sortable_columns' );

function custom_todo_sortable_columns( $columns )
{
	$columns['complete'] = __('Complete', 'import-todos');

	return $columns;
}


// Populate columns with data
add_action('manage_todo_posts_custom_column', 'custom_todo_column', 10, 2);

function custom_todo_column($column, $post_id)
{
	// Status column
	if ( 'complete' === $column ) {
		$value = get_post_meta( $post_id, 'remote_completed', true );

		echo $value ? 'checked' : 'not checked';
	}
}


// Define what to sort custom fields by
add_action('pre_get_posts', 'todo_orderby');

function todo_orderby( $query )
{
	if ( !is_admin() || !$query->is_main_query() ) {
		return;
	}

	if ( 'status' === $query->get('orderby') ) {
		$query->set('orderby', 'meta_value');
		$query->set('meta_key', 'remote_completed');
	}
}
