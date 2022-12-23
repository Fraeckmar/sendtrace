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
			'name'					=> esc_html_x('Shipment', 'Shipment', 'shiptrack'),
			'singular_name'			=> esc_html_x('Shipment', 'Shipment', 'shiptrack'),
			'menu_name' 			=> esc_html__('Shipment', 'shiptrack'),
			'all_items' 			=> esc_html__('All Shipments', 'shiptrack'),
			'view_item' 			=> esc_html__('View Shipment', 'shiptrack'),
			'add_new_item' 			=> esc_html__('Add New Shipment', 'shiptrack'),
			'add_new' 				=> esc_html__('Add Shipment', 'shiptrack'),
			'edit_item' 			=> esc_html__('Edit Shipment', 'shiptrack'),
			'update_item' 			=> esc_html__('Update Shipment', 'shiptrack'),
			'search_items' 			=> esc_html__('Search Shipment', 'shiptrack'),
			'not_found' 			=> esc_html__('Shipment Not found', 'shiptrack'),
			'not_found_in_trash' 	=> esc_html__('Shipment Not found in Trash', 'shiptrack')
		);

		$shiptrack_supports 			= array( 'title', 'author', 'thumbnail', 'revisions' );
		$args_tag         			= array(
			'label' 				=> esc_html__('Shipment', 'shiptrack'),
			'description' 			=> esc_html__('Shipment', 'shiptrack'),
			'labels' 				=> $labels_menu,
			'supports' 				=> $shiptrack_supports,
			'taxonomies' 			=> array( 'shiptrack', 'post_tag' ),
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

		register_post_type('shiptrack', $args_tag);
    }
}

WPSTPostType::init();