<?php

class WPSTMenu
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'wpst_menus_cb'));
    }

    function wpst_menus_cb()
    {
        $in_edit_page = isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['action']) && wpst_sanitize_data($_GET['action']) == 'edit';
        add_menu_page(
            wpst_label(),
            wpst_label(),
            'edit_posts',
            wpst_plugin_slug(),
            array($this, 'wpst_manage_shipments'),
            'dashicons-pressthis',
            5
        );

        // Manage Shipments
        add_submenu_page(
            wpst_plugin_slug(),
            wpst_shipments_label(),
            wpst_shipments_label(),
            'edit_posts',
            wpst_plugin_slug()
        );

        // New Shipment
        add_submenu_page(
            wpst_plugin_slug(),
            $in_edit_page ? wpst_edit_shipment_label() : wpst_new_shipment_label(),
            wpst_shipment_label(),
            'edit_posts',
            wpst_plugin_slug().'-item',
            array($this, 'wpst_shiptrack_post')
        );

        // Settings
        add_submenu_page(
            wpst_plugin_slug(),
            wpst_settings_label(),
            wpst_settings_label(),
            'manage_options',
            wpst_plugin_slug().'-settings',
            array($this, 'wpst_shiptrack_settings')
        );

        // Report
        add_submenu_page(
            wpst_plugin_slug(),
            __('Reports', 'shiptrack'),
            __('Reports', 'shiptrack'),
            'manage_options',
            wpst_plugin_slug().'-report',
            array($this, 'wpst_shiptrack_reports')
        );
    }

    function wpst_manage_shipments()
    {
        global $shiptrack, $WPSTField;
        $plugin_slug = wpst_plugin_slug();
        if (!isset($_GET['page']) && wpst_sanitize_data($_GET['page']) != $plugin_slug) { 
            return false; 
        }

        $current_user_role = wpst_get_user_role();
        $user_is_admin = wpst_is_user_admin();
        $assigned_role = str_replace('shiptrack_', '', $current_user_role);
        $user_can_delete = wpst_shipment_user_can('delete');
        $user_can_update = wpst_shipment_user_can('update');
        $last_shipment_date = wpst_get_last_date_of_shipments();
        $last_month = !empty($last_shipment_date) ? date('m', strtotime($last_shipment_date)) : date('m');
        $last_day = !empty($last_shipment_date) ? date('j', strtotime($last_shipment_date)) : date('j');

        $shipment_id = isset($_GET['id']) && is_numeric(wpst_sanitize_data($_GET['id'])) ? wpst_sanitize_data($_GET['id']) : 0;
        $action = isset($_GET['action']) ? wpst_sanitize_data($_GET['action']) : '';      
        $status = isset($_GET['status']) ? wpst_sanitize_data($_GET['status']) : '';  
        $is_active_shipments = !in_array($action, array('untrash', 'delete')) && !in_array($status, array('trash', 'untrash')) ? true : false;

        $q_shipment = isset($_GET['q_shipment']) && !empty($_GET['q_shipment']) ? sanitize_text_field($_GET['q_shipment'])  : '';
        $q_shipper_name = isset($_GET[wpst_customer_field('shipper', 'key')]) ? sanitize_text_field($_GET[wpst_customer_field('shipper', 'key')]) : '';
        $q_receiver_name = isset($_GET[wpst_customer_field('receiver', 'key')]) ? sanitize_text_field($_GET[wpst_customer_field('receiver', 'key')]) : '';
        $q_shiptrack_status = isset($_GET['shiptrack_status']) ? sanitize_text_field($_GET['shiptrack_status']) : '';
        $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : date(wpst_date_format(), strtotime(date("Y-{$last_month}-1")));
        $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : date(wpst_date_format(), strtotime(date("Y-{$last_month}-{$last_day}")));

        if (empty($date_from) && empty($date_to) && isset($_GET['date_from']) && isset($_GET['date_to'])) {
            $date_from = wpst_decrypt_param(wpst_sanitize_data($_GET['date_from']));
            $date_to = wpst_decrypt_param(wpst_sanitize_data($_GET['date_to']));
        }

        $post_per_page = isset($_GET['post_per_page']) && is_numeric($_GET['post_per_page']) ? sanitize_text_field($_GET['post_per_page'])  : 10;
        $entries_options = array(10, 25, 50);
        $meta_query = array();
        if (!empty($q_shipper_name)) {
            $meta_query[] = array(
                'key' => wpst_customer_field('shipper', 'key'),
                'value' => $q_shipper_name,
                'compare' => '='
            );
        }
        if (!empty($q_receiver_name)) {
            $meta_query[] = array(
                'key' => wpst_customer_field('receiver', 'key'),
                'value' => $q_receiver_name,
                'compare' => '='
            );
        }
        if (!empty($q_shiptrack_status)) {
            $meta_query[] = array(
                'key' => 'shiptrack_status',
                'value' => $q_shiptrack_status,
                'compare' => '='
            );
        }

        if (!$user_is_admin && (!wpst_editor_can_acess_all_shipments() && $current_user_role == 'shiptrack_editor')) {
            $meta_query[] = array(
                'key' => 'assigned_'.$assigned_role,
                'value' => get_current_user_id(),
                'compare' => '='
            );
        }
        
        $meta_query = apply_filters( 'wpst_manage_shipments_meta_query', $meta_query);
        $paged = isset($_GET['paged']) && is_numeric($_GET['paged']) ? sanitize_text_field($_GET['paged']) : 1; 
        
        // Active Shipments Query
        $active_args = array(
            'post_type'         => 'shiptrack',
            'post_status'       => 'publish',
            'posts_per_page'    => $post_per_page,
            'paged'             => $paged,
            's'                 => $q_shipment,
            'meta_query' => array(
                'relation' => 'AND',
                $meta_query
            )
        );

        if (!empty($date_from) || !empty($date_to)) {
            $active_args['date_query'] = array();
            if (!empty($date_from)) {
                $active_args['date_query']['after'] = array(
                    'year' => date('Y', strtotime($date_from)),
                    'month' => date('m', strtotime($date_from)),
                    'day' => date('d', strtotime($date_from))
                );
            }
            if (!empty($date_to)) {
                $active_args['date_query']['before'] = array(
                    'year' => date('Y', strtotime($date_to)),
                    'month' => date('m', strtotime($date_to)),
                    'day' => date('d', strtotime($date_to))
                );
            }
            $active_args['date_query']['inclusive'] = true;
        }

        // Trashed Shipments Query
        $trash_meta_query = array();
        if (!$user_is_admin) {
            $trash_meta_query[] = array(
                'key' => 'assigned_'.$assigned_role,
                'value' => get_current_user_id(),
                'compare' => '='
            );
        }

        $trash_args = array(
            'post_type'         => 'shiptrack',
            'post_status'       => 'trash',
            'posts_per_page'    => $post_per_page,
            'paged'             => $paged,
            's'                 => $q_shipment,
            'meta_query' => array(
                'relation' => 'AND',
                $trash_meta_query
            )
        );

        $active_args = apply_filters( 'wpst_manage_shipments_args', $active_args );
        $active_shipments  = new WP_Query($active_args);
        $trash_shipments  = new WP_Query($trash_args);
        $active_count = $active_shipments->found_posts;
        $trash_count = $trash_shipments->found_posts;
        $shipments =  $is_active_shipments ? $active_shipments : $trash_shipments;   
        $number_records = $shipments->found_posts;
        $basis = $paged * $post_per_page;
        $record_end     = $number_records < $basis ? $number_records : $basis ;
        $record_start   = $basis - ($post_per_page - 1);   
        $bulk_update_label = $is_active_shipments ? 'Bulk Trash' : 'Bulk Delete';
        $status_attr = $is_active_shipments ? 'data-status=trash' : 'data-status=delete';
        require_once wpst_get_template('shipments.tpl');
    }

    function wpst_shiptrack_post()
    {
        global $shiptrack, $WPSTField;
        $plugin_slug = wpst_plugin_slug();
        $shipment_id = isset($_GET['id']) && is_numeric(wpst_sanitize_data($_GET['id'])) ? wpst_sanitize_data($_GET['id']) : 0;
        $shiptrack_status = !empty($shipment_id) ? wpst_sanitize_data(get_post_meta($shipment_id, 'shiptrack_status', true)) : '';
        $action = isset($_GET['action']) ? wpst_sanitize_data($_GET['action']) : 'new';
        if ($action == 'edit' && !$shipment_id) {
            $action = 'new';
        }

        if (in_array($action, array('new', 'edit', 'view'))) {
            $order_id = get_post_meta($shipment_id, 'order_id', true);
            $form_fields = !empty($WPSTField->fields()) ? $WPSTField->fields($shipment_id) : array();
            $title = in_array($action, array('view', 'edit')) ? get_the_title($shipment_id) : '';
            if ($action == 'new' && wpst_is_tracking_auto_generate()) {
                $title = $shiptrack->generate_tracking_no();
            }
        }
        $role_action = $action;
        if ($action == 'new') {
            $role_action = 'create';
        }
        if ($action == 'edit') {
            $role_action = 'update';
        }
        if ($action == 'view') {
            $role_action = 'read';
        }
        if (wpst_shipment_user_can($role_action)) {
            require_once wpst_get_template('shipment.tpl');
        } else {
            $error_msg = 'Sorry you don\'t have access to '.esc_html($role_action). ' shipment.';
            wpst_error_handler($error_msg, 'mt-3 h5 fw-normal');
        }        
    }

    function wpst_shiptrack_settings()
    {
        global $shiptrack, $WPSTField;
        $current_tab = isset($_GET['tab']) && !empty($_GET['tab']) ? wpst_sanitize_data($_GET['tab']) : 'general';
        $general_setting_fields = array_key_exists('general', $WPSTField->settings_field()) ? $WPSTField->settings_field()['general'] : array();
        $admin_email_fields = array_key_exists('email_admin', $WPSTField->settings_field()) ? $WPSTField->settings_field()['email_admin'] : array();
        $client_email_fields = array_key_exists('email_client', $WPSTField->settings_field()) ? $WPSTField->settings_field()['email_client'] : array();

        echo "<div id='shiptrack-admin' class='wrap shiptrack'>";
            require_once(WPST_PLUGIN_PATH. 'templates/admin/settings/navigation.tpl.php');
            echo "<div id='wpst-setting-content' class='p-1'>";
                foreach ($shiptrack->settings_menu() as $menu_key => $menu) {
                    $menu_key = sanitize_key($menu_key);
                    require_once $menu['file_path'];
                }
            echo "</div>";
        echo "</div>";
    }

    function wpst_shiptrack_reports()
    {
        global $shiptrack;
        $status_list = $shiptrack->status_list();
        $client_list = wpst_get_users_list(['shiptrack_client']);
        $assigned_client = isset($_POST['assigned_client']) ? wpst_sanitize_data($_POST['assigned_client']) : '';
        $shipper_name = isset($_POST['wpst_shipper_name']) ? wpst_sanitize_data($_POST['wpst_shipper_name']) : '';
        $shiptrack_status = isset($_POST['shiptrack_status']) ? wpst_sanitize_data($_POST['shiptrack_status']) : '';
        $date_from = isset($_POST['date_from']) ? wpst_sanitize_data($_POST['date_from']) : '';
        $date_to = isset($_POST['date_to']) ? wpst_sanitize_data($_POST['date_to']) : '';
        require_once wpst_get_template('admin/reports.tpl');
    }
}

new WPSTMenu;