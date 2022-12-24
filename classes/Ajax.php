<?php

class WPSTAjax
{
    function __construct()
    {
        add_action('wp_ajax_bulk_update_post_status', array($this, 'wpst_bulk_update_post_status'));
        add_action('wp_ajax_print_shipment_pdf', array($this, 'wpst_print_shipment_pdf'));
        add_action('wp_ajax_nopriv_print_shipment_pdf', array($this, 'wpst_print_shipment_pdf'));
        add_action('wp_ajax_selectize_search', array($this, 'wpst_selectize_search_callback'));
        add_action('wp_ajax_nopriv_selectize_search', array($this, 'wpst_selectize_search_callback'));
        add_action('wp_ajax_generate_report', array($this, 'wpst_generate_report'));
        add_action('wp_ajax_nopriv_generate_report', array($this, 'wpst_generate_report'));
    }

    function wpst_selectize_search_callback()
    {
        global $wpdb;
        $meta_key = isset($_POST['meta_key']) ? wpst_sanitize_data($_POST['meta_key']) : '';
        $q = isset($_POST['q']) ? wpst_sanitize_data($_POST['q']) : '';

        $sql = "SELECT DISTINCT pm.meta_value FROM `{$wpdb->prefix}posts` p INNER JOIN `{$wpdb->prefix}postmeta` pm ON p.ID = pm.post_id WHERE p.post_type = 'sendtrace' AND p.post_status = 'publish' AND pm.meta_key = '{$meta_key}' AND pm.meta_value LIKE '%{$q}%'";
        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            $new_result = array();
            foreach ($results as $result) {
                $new_result[][$meta_key] = $result->meta_value;
            }
            $results = $new_result;
        }
        echo json_encode($results);
        die();
    }

    function wpst_bulk_update_post_status()
    {
        $shipmet_ids = isset($_POST['shipment_ids']) ? wpst_sanitize_data($_POST['shipment_ids']) : array();
        $post_status = isset($_POST['status']) ? wpst_sanitize_data($_POST['status']) : '';
        $result = array('status' => 'error');
        
        if (empty($shipmet_ids) || empty($post_status)) {
            if (empty($shipmet_ids)) {
                $result['error'] = __('No shipment(s) selected.', 'wpst');
            }
            if (empty($post_status)) {
                $result['error'] = __('Status is not set for this action.', 'wpst');
            }
        } else {
            try {
                foreach ($shipmet_ids as $shipment_id) {
                    if ($post_status == 'delete') {
                        wp_delete_post($shipment_id, true);
                    } else {
                        wpst_update_post_status($shipment_id, $post_status);
                    }                
                }
                $result['status'] = 'ok';
                $post_status = $post_status == 'publish' ? 'restore' : $post_status;
                $result['msg'] = esc_html('Selected shipment(s) '.$post_status.' successfully.');
            } catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }        
        }
        echo json_encode($result);
        die();
    }

    function wpst_print_shipment_pdf()
    {
        $shipment_id = isset($_POST['shipment_id']) && is_numeric($_POST['shipment_id']) ? wpst_sanitize_data($_POST['shipment_id']) : 0;
        $type = isset($_POST['type']) ? wpst_sanitize_data($_POST['type']) : '';

        require_once WPST_PLUGIN_PATH. 'classes/Pdf.php';
        $pdf = new WPSTPdf($shipment_id);
        echo json_encode($pdf->print($type));
        die();
    }

    // Generate Report
    function wpst_generate_report()
    {
        global $sendtrace, $WPSTField;
        require_once WPST_PLUGIN_PATH. 'classes/Report.php';
        $report = new WPSTReport;

        $assigned_client = isset($_POST['assigned_client']) && is_numeric($_POST['assigned_client']) ? sanitize_text_field($_POST['assigned_client']) : 0;
        $shipper = isset($_POST['shipper']) ? sanitize_text_field($_POST['shipper']) : '';
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        $date_from = isset($_POST['date_from']) ? sanitize_text_field($_POST['date_from']) : '';
        $date_to = isset($_POST['date_to']) ? sanitize_text_field($_POST['date_to']) : '';

        $meta_query = array();
        if (!empty($shipper)) {
            $meta_query[] = array(
                'key' => wpst_customer_field('shipper', 'key'),
                'value' => trim($shipper),
                'compare' => '='
            );
        }
        if (!empty($status)) {
            $meta_query[] = array(
                'key' => 'sendtrace_status',
                'value' => trim($status),
                'compare' => '='
            );
        }
        if ($assigned_client) {
            $meta_query[] = array(
                'key' => 'assigned_client',
                'value' => trim($assigned_client),
                'compare' => '='
            );
        }
        $args = array(
            'post_type' => 'sendtrace',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                $meta_query
            )
        );

        if (!empty($date_from) || !empty($date_to)) {
            $args['date_query'] = array();
            if (!empty($date_from)) {
                $args['date_query']['after'] = array(
                    'year' => date('Y', strtotime($date_from)),
                    'month' => date('m', strtotime($date_from)),
                    'day' => date('d', strtotime($date_from))
                );
            }
            if (!empty($date_to)) {
                $args['date_query']['before'] = array(
                    'year' => date('Y', strtotime($date_to)),
                    'month' => date('m', strtotime($date_to)),
                    'day' => date('d', strtotime($date_to))
                );
            }
            $args['date_query']['inclusive'] = true;
        }

        $result = array('status'=>'error');
        $posts = get_posts($args);
        $custom_fields = $WPSTField->fields();

        if (empty($posts)) {
            $result['error'] = __('No record(s) found.', 'sendtrace');
        }
        if (empty($custom_fields)) {
            $result['error'] = __('Custom fields is empty.', 'sendtrace');
        }

        $report_data = array();
        $report_headers = array(
            'tracking_no' => __('Tracking No.', 'sendtrace'), 
            'post_date' => __('Date Created', 'sendtrace'),
            'sendtrace_status' => __('Status', 'sendtrace'),
            'assigned_client' => __('Assigned Client', 'sendtrace')
        );        

        if (!empty($posts) && !empty($custom_fields)) {
            foreach ($posts as $post) {
                $meta_values = $sendtrace->get_shipment_details($post->ID);
                $report_data[$post->ID]['tracking_no'] = $post->post_title;
                $report_data[$post->ID]['post_date'] = date(wpst_date_format(), strtotime($post->post_date));
                $report_data[$post->ID]['sendtrace_status'] = get_post_meta($post->ID, 'sendtrace_status', true);
                $report_data[$post->ID]['assigned_client'] = wpst_get_user_data(get_post_meta($post->ID, 'assigned_client', true), 'display_name');
                foreach ($custom_fields as $section) {
                    foreach ($section['fields'] as $field) {
                        $meta_value = array_key_exists($field['key'], $meta_values) ? $meta_values[$field['key']] : '';
                        if (is_array($meta_value)) {
                            $meta_value = implode(', ', array_filter($meta_value));
                        }
                        $meta_value = apply_filters('wpst_report_data', $meta_value, $field['key'], $post->ID);
                        $report_data[$post->ID][$field['key']] = wpst_sanitize_data($meta_value);
                        $report_headers[$field['key']] = esc_html($field['label']);
                    }
                }
            
                $report_data[$post->ID]['packages'] = wpst_get_packages_data($post->ID, true, true);
            }
            $report_headers['packages'] = __('Pacakges', 'sendtrace');
        }

        $format = wpst_sanitize_data(apply_filters('wpst_report_file_format', 'csv'));
        $report_headers = wpst_sanitize_data(apply_filters('wpst_report_hearders', $report_headers, $custom_fields));
        $report_data = wpst_sanitize_data(apply_filters('wpst_report_data', $report_data, $posts));
        $report_result = $report->create_report($report_headers, $report_data, $format);  

        if (!array_key_exists('error', $result)) {
            $result['status'] = 'ok';
            $result['fileurl'] = $report_result['fileurl'];
            $result['filename'] = $report_result['filename'];
            $result['msg'] = '('.count($posts).') record(s) generated.';
        }
        
        echo json_encode($result);
        die();
    }
}
new WPSTAjax;