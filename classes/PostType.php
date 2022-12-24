<?php

class WPSTPostType
{
    static function init() 
    {
        add_action('admin_init', array(__CLASS__, 'wpst_custom_post_type'), 1);
    }
    
    static function wpst_custom_post_type()
    {
        $labels_menu = array(
			'name'					=> esc_html_x('Shipment', 'Shipment', 'sendtrace'),
			'singular_name'			=> esc_html_x('Shipment', 'Shipment', 'sendtrace'),
			'menu_name' 			=> esc_html__('Shipment', 'sendtrace'),
			'all_items' 			=> esc_html__('All Shipments', 'sendtrace'),
			'view_item' 			=> esc_html__('View Shipment', 'sendtrace'),
			'add_new_item' 			=> esc_html__('Add New Shipment', 'sendtrace'),
			'add_new' 				=> esc_html__('Add Shipment', 'sendtrace'),
			'edit_item' 			=> esc_html__('Edit Shipment', 'sendtrace'),
			'update_item' 			=> esc_html__('Update Shipment', 'sendtrace'),
			'search_items' 			=> esc_html__('Search Shipment', 'sendtrace'),
			'not_found' 			=> esc_html__('Shipment Not found', 'sendtrace'),
			'not_found_in_trash' 	=> esc_html__('Shipment Not found in Trash', 'sendtrace')
		);

		$sendtrace_supports 			= array( 'title', 'author', 'thumbnail', 'revisions' );
		$args_tag         			= array(
			'label' 				=> esc_html__('Shipment', 'sendtrace'),
			'description' 			=> esc_html__('Shipment', 'sendtrace'),
			'labels' 				=> $labels_menu,
			'supports' 				=> $sendtrace_supports,
			'taxonomies' 			=> array( 'sendtrace', 'post_tag' ),
			'menu_icon' 			=> 'dashicons-book-alt',
			'hierarchical' 			=> true,
			'public' 				=> true,
			'show_ui' 				=> true,
			'show_in_menu' 			=> true,
			'show_in_nav_menus' 	=> true,
			'show_in_admin_bar' 	=> true,
			'menu_position' 		=> 5,
			'can_export' 			=> true,
			'has_archive' 			=> false,
			'exclude_from_search' 	=> true,
			'publicly_queryable' 	=> false,
			'capability_type' 		=> 'post'
		);

		register_post_type('sendtrace', $args_tag);
    }
}

WPSTPostType::init();