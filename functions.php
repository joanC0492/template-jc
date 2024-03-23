<?php

// Editor clasico
add_filter('use_block_editor_for_post', '__return_false', 10);
add_post_type_support('page', 'excerpt');

if (function_exists('add_theme_support')) {
	// Add Menu Support
	add_theme_support('menus');

	// Add Thumbnail Theme Support
	add_theme_support('post-thumbnails');
	add_image_size('large', 700, '', true); // Large Thumbnail
	add_image_size('medium', 250, '', true); // Medium Thumbnail
	add_image_size('small', 120, '', true); // Small Thumbnail
	add_image_size('custom-size', 700, 200, true); // Custom Thumbnail Size 

	// Enables post and comment RSS feed links to head
	add_theme_support('automatic-feed-links');

	// Localisation Support
	load_theme_textdomain('html5blank', get_template_directory() . '/languages');
}

/*
 * ========================================================================
 * Functions
 * ========================================================================
 */

// Register HTML5 Blank's Navigation
function register_html5_menu()
{
	register_nav_menus(array( // Using array to specify more menus if needed
		'header-menu' => __('Header Menu', 'html5blank'), // Main Navigation
		'sidebar-menu' => __('Sidebar Menu', 'html5blank'), // Sidebar Navigation
		'extra-menu' => __('Extra Menu', 'html5blank'), // Extra Navigation if needed (duplicate as many as you need!)
		'footer-1-menu' => __('Footer Menu1', 'html5blank'),
		'footer-2-menu' => __('Footer Menu2', 'html5blank'),
		'footer-3-menu' => __('Footer Menu3', 'html5blank'),
	));
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist)
{
	return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes)
{
	global $post;
	if (is_home()) {
		$key = array_search('blog', $classes);
		if ($key > -1) {
			unset($classes[$key]);
		}
	} elseif (is_page()) {
		$classes[] = sanitize_html_class($post->post_name);
	} elseif (is_singular()) {
		$classes[] = sanitize_html_class($post->post_name);
	}

	return $classes;
}

// If Dynamic Sidebar Exists
if (function_exists('register_sidebar')) {
	// Define Sidebar Widget Area 1
	register_sidebar(array(
		'name' => __('Widget Area 1', 'html5blank'),
		'description' => __('Description for this widget-area...', 'html5blank'),
		'id' => 'widget-area-1',
		'before_widget' => '<div id="%1$s" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));

	// Define Sidebar Widget Area 2
	register_sidebar(array(
		'name' => __('Widget Area 2', 'html5blank'),
		'description' => __('Description for this widget-area...', 'html5blank'),
		'id' => 'widget-area-2',
		'before_widget' => '<div id="%1$s" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));
}

// Remove wp_head() injected Recent Comment styles
function my_remove_recent_comments_style()
{
	global $wp_widget_factory;
	remove_action('wp_head', array(
		$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
		'recent_comments_style'
	));
}

// Remove Admin bar
function remove_admin_bar()
{
	return false;
}

// Remove 'text/css' from our enqueued stylesheet
function html5_style_remove($tag)
{
	return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions($html)
{
	$html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
	return $html;
}

// Custom Gravatar in Settings > Discussion
function html5blankgravatar($avatar_defaults)
{
	$myavatar = get_template_directory_uri() . '/img/gravatar.jpg';
	$avatar_defaults[$myavatar] = "Custom Gravatar";
	return $avatar_defaults;
}

/*
 * ========================================================================
 * Actions + Filters + ShortCodes
 * ========================================================================
 */

// Add Actions
add_action('init', 'register_html5_menu'); // Add HTML5 Blank Menu
add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

// Add Filters
add_filter('avatar_defaults', 'html5blankgravatar'); // Custom Gravatar in Settings > Discussion
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
add_filter('show_admin_bar', 'remove_admin_bar'); // Remove Admin bar
add_filter('style_loader_tag', 'html5_style_remove'); // Remove 'text/css' from enqueued stylesheet
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('image_send_to_editor', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to post images


/*--------------------------MI CODIGO---------------------------------- */
// Agregando mis assets | "wp_head()"
function assets()
{
	// Registramos estilos
	wp_register_style("font", "https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap", array(), false, "all");
	// Agregamos los estilos tailwind luego de la fuente
	wp_enqueue_style("tailwind", get_template_directory_uri() . "/assets/css/tw.css", array("font"));
}

// Agregar codigo al iniciar el body | "wp_body_open()"
function my_custom_message_in_body()
{
	echo '<p>¡Bienvenido a mi sitio web!</p>';
}



/*
========================================================================
	Custom Post Types
========================================================================
*/
/*
========================================================================
	Shortcodes
========================================================================
*/



// Add Filters
add_action("wp_enqueue_scripts", "assets");
add_action('wp_body_open', 'my_custom_message_in_body');
