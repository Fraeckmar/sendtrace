<?php

class WPSTField
{
    function fields($shipment_id=0) {
        global $shiptrack;
        $fields = array(
            'shipper_information' => array(
                'section_col' => '6',
                'field_col' => '12',
                'heading' => __('Shipper Information'),
                'fields' => array(
                    'wpst_shipper_name' => array(
                        'key' => 'wpst_shipper_name',
                        'label' => __('Shipper Name', 'shiptrack'),
                        'type' => 'text',
                        'required' => true
                    ),
                    'wpst_shipper_phone_number' => array(
                        'key' => 'wpst_shipper_phone_number',
                        'label' => __('Phone Number', 'shiptrack'),
                        'type' => 'number',
                        'extras' => 'step=any'
                    ),
                    'wpst_shipper_email' => array(
                        'key' => 'wpst_shipper_email',
                        'label' => __('Email', 'shiptrack'),
                        'type' => 'email',
                    ),
                    'wpst_shipper_address' => array(
                        'key' => 'wpst_shipper_address',
                        'label' => __('Address', 'shiptrack'),
                        'type' => 'address',
                    )
                )
            ),
            'receiver_information' => array(
                'section_col' => '6',
                'field_col' => '12',
                'heading' => __('Receiver Information'),
                'fields' => array(
                    'wpst_receiver_name' => array(
                        'key' => 'wpst_receiver_name',
                        'label' => __('Receiver Name', 'shiptrack'),
                        'type' => 'text',
                        'required' => true
                    ),
                    'wpst_receiver_phone_number' => array(
                        'key' => 'wpst_receiver_phone_number',
                        'label' => __('Phone Number', 'shiptrack'),
                        'type' => 'number',
                        'extras' => 'step=any'
                    ),
                    'wpst_receiver_email' => array(
                        'key' => 'wpst_receiver_email',
                        'label' => __('Email', 'shiptrack'),
                        'type' => 'email',
                    ),
                    'wpst_receiver_address' => array(
                        'key' => 'wpst_receiver_address',
                        'label' => __('Address', 'shiptrack'),
                        'type' => 'address',
                    )
                )
            ),
            'shipment_details' => array(
                'section_col' => '12',
                'field_col' => '6',
                'heading' => __('Shipment Details'),
                'fields' => array(
                    'wpst_transporation_mode' => array(
                        'key' => 'wpst_transporation_mode',
                        'label' => __('Transportation Mode', 'shiptrack'),
                        'type' => 'select',
                        'options' => $shiptrack->shipment_types(),
                        'class' => 'w-100'
                    ),
                    'wpst_courier' => array(
                        'key' => 'wpst_courier',
                        'label' => __('Courier', 'shiptrack'),
                        'type' => 'text',
                    ),
                    'wpst_carrier' => array(
                        'key' => 'wpst_carrier',
                        'label' => __('Carrier', 'shiptrack'),
                        'type' => 'select',
                        'options' => $shiptrack->carriers(),
                        'class' => 'w-100'
                    ),
                    'wpst_origin' => array(
                        'key' => 'wpst_origin',
                        'label' => __('Origin', 'shiptrack'),
                        'type' => 'text',
                    ),
                    'wpst_destination' => array(
                        'key' => 'wpst_destination',
                        'label' => __('Destination', 'shiptrack'),
                        'type' => 'text',
                    ),
                    'wpst_pickup_date' => array(
                        'key' => 'wpst_pickup_date',
                        'label' => __('Pickup Date', 'shiptrack'),
                        'type' => 'date',
                    ),
                    'wpst_pickup_time' => array(
                        'key' => 'wpst_pickup_time',
                        'label' => __('Pickup Time', 'shiptrack'),
                        'type' => 'time'
                    ),
                    'wpst_departure_time' => array(
                        'key' => 'wpst_departure_time',
                        'label' => __('Departure Time', 'shiptrack'),
                        'type' => 'time',
                    ),
                    'wpst_expected_delivery_date' => array(
                        'key' => 'wpst_expected_delivery_date',
                        'label' => __('Expected Delivery Date', 'shiptrack'),
                        'type' => 'date',
                    ),
                    'wpst_remarks' => array(
                        'key' => 'wpst_remarks',
                        'label' => __('Remarks', 'shiptrack'),
                        'type' => 'textarea',
                        'class' => 'w-100'
                    ),
                )
            )
        );
        
        if ($shipment_id) {
            foreach ($fields as $section => $info) {
                foreach ($info['fields'] as $field) {
                    $field_value = wpst_sanitize_data(get_post_meta($shipment_id, $field['key'], true)) ?? '';
                    $fields[$section]['fields'][$field['key']]['value'] = $field_value;
                }
            }
        }

        return apply_filters('wpst_fields', $fields, $shipment_id);
    }

    function shipment_fields_only($shipment_id=0) {
        return wpst_merge_array_values(array_column($this->fields($shipment_id), 'fields'));
    }

    function settings_field() {
        global $shiptrack;
        $admin_default_email = sanitize_email(get_bloginfo('admin_email'));
        $status_list = $shiptrack->status_list();
        $fields = array(
            'general' => array(
                array(
                    'heading' => __('Appearance & Company Info', 'shiptrack'),
                    'fields' => array(
                        array(
                            'key' => 'company_logo',
                            'label' => __('Compay Logo', 'shiptrack'),
                            'type' => 'upload',
                            'value' => $shiptrack->get_setting('general', 'company_logo'),
                            'setting' => 'general',
                            'group_class' => 'mb-0'
                        ),
                        array(
                            'key' => 'bg_color',
                            'label' => __('Background Color', 'shiptrack'),
                            'type' => 'color',
                            'class' => 'form-control-color',
                            'value' => wpst_bg_color(),
                            'setting' => 'general'
                        ),
                        array(
                            'key' => 'fg_color',
                            'label' => __('Foreground Color', 'shiptrack'),
                            'type' => 'color',
                            'class' => 'form-control-color',
                            'value' => wpst_fg_color(),
                            'setting' => 'general'
                        )
                    )
                ),
                array(
                    'heading' => __('Shipment', 'shiptrack'),
                    'fields' => array(
                        array(
                            'key' => 'auto_generate',
                            'label' => __('Auto Generate Tracking No.?', 'shiptrack'),
                            'type' => 'radio',
                            'options' => array('Yes', 'No'),
                            'value' => $shiptrack->get_setting('general', 'auto_generate', 'Yes'),
                            'setting' => 'general'
                        ),
                        array(
                            'key' => 'tracking_no_length',
                            'label' => __('Tracking No. Length', 'shiptrack'),
                            'type' => 'number',
                            'value' => !empty($shiptrack->get_setting('general', 'tracking_no_length')) ? $shiptrack->get_setting('general', 'tracking_no_length') : 8,
                            'setting' => 'general',
                            'description' => '<strong>Note:</strong> Prefix and Suffix are not include.'
                        ),
                        array(
                            'key' => 'tracking_no_prefix',
                            'label' => __('Tracking No. Prefix', 'shiptrack'),
                            'type' => 'text',
                            'value' => !empty($shiptrack->get_setting('general', 'tracking_no_prefix')) ? $shiptrack->get_setting('general', 'tracking_no_prefix') : 'SHIP',
                            'setting' => 'general'
                        ),
                        array(
                            'key' => 'tracking_no_suffix',
                            'label' => __('Tracking No. Suffix', 'shiptrack'),
                            'type' => 'text',
                            'value' => $shiptrack->get_setting('general', 'tracking_no_suffix'),
                            'setting' => 'general'
                        ),
                        array(
                            'key' => 'status_list',
                            'label' => __('Status Options', 'shiptrack'),
                            'type' => 'select',
                            'class' => 'selectize rounded border form-control selectize-set-min-height',
                            'options' => $shiptrack->status_list(),
                            'value' => $shiptrack->status_list(),
                            'setting' => 'general',
                            'extras' => 'multiple data-allow_create=true data-has_remove=true style=min-height:55px'
                        ),
                        array(
                            'key' => 'package_types',
                            'label' => __('Package Types', 'shiptrack'),
                            'type' => 'select',
                            'class' => 'selectize rounded border form-control selectize-set-min-height',
                            'options' => $shiptrack->package_types(),
                            'value' => $shiptrack->package_types(),
                            'setting' => 'general',
                            'extras' => 'multiple data-allow_create=true data-has_remove=true style=min-height:55px'
                        ),
                        array(
                            'key' => 'transportation_mode',
                            'label' => __('Transportation Mode', 'shiptrack'),
                            'type' => 'select',
                            'class' => 'selectize rounded border form-control selectize-set-min-height',
                            'options' => $shiptrack->shipment_types(),
                            'value' => $shiptrack->shipment_types(),
                            'setting' => 'general',
                            'extras' => 'multiple data-allow_create=true data-has_remove=true style=min-height:55px'
                        ),
                        array(
                            'key' => 'carriers',
                            'label' => __('Carrier Options', 'shiptrack'),
                            'type' => 'select',
                            'class' => 'selectize rounded border form-control selectize-set-min-height',
                            'options' => $shiptrack->carriers(),
                            'value' => $shiptrack->carriers(),
                            'setting' => 'general',
                            'extras' => 'multiple data-allow_create=true data-has_remove=true style=min-height:55px'
                        ),
                        array(
                            'key' => 'roles_modify_history',
                            'label' => __('Roles can modify shipment history', 'shiptrack'),
                            'type' => 'checkbox',
                            'options' => wpst_get_user_roles(),
                            'value' => $shiptrack->get_setting('general', 'roles_modify_history'),
                            'setting' => 'general'
                        )
                    )
                ),
                array(
                    'heading' => __('Multiple Pacakge', 'shiptrack'),
                    'fields' => array(
                        array(
                            'key' => 'weight_unit',
                            'label' => __('Weight Unit', 'shiptrack'),
                            'type' => 'text',
                            'value' => $shiptrack->weight_unit_used(),
                            'setting' => 'general'
                        ),
                        array(
                            'key' => 'dim_unit',
                            'label' => __('Dimension Unit', 'shiptrack'),
                            'type' => 'text',
                            'value' => $shiptrack->dim_unit_used(),
                            'setting' => 'general'
                        ),
                        array(
                            'key' => 'volumetric_weight_divisor',
                            'label' => __('Volumetric Weight Dvisor', 'shiptrack'),
                            'description' => '<strong>Note</strong>: Use to get volumetric weight: (L*W*H) / Divisor',
                            'type' => 'text',
                            'value' => $shiptrack->get_volumetric_weight_divisor(),
                            'setting' => 'general'
                        ),
                    )
                ),
                array(
                    'heading' => __('Fees', 'shiptrack'),
                    'fields' => array(
                        array(
                            'key' => 'tax',
                            'label' => __('Tax in (%)', 'shiptrack'),
                            'description' => '<strong>Note</strong>: This will apply all payment transactions.',
                            'type' => 'number',
                            'value' => $shiptrack->tax,
                            'extras' => 'step=any',
                            'setting' => 'general'
                        ),
                    )
                )
            ),
            'email_admin' => array(
                array(
                    'heading' => __('Admin Email Setting', 'shiptrack'),
                    'fields' => array(
                        array(
                            'key' => 'admin_enable',
                            'label' => __('Enable?', 'shiptrack'),
                            'type' => 'radio',
                            'required' => true,
                            'class' => '',
                            'group_class' => 'form-check-inline',
                            'options' => array('Yes', 'No'),
                            'value' => $shiptrack->get_setting('email_admin', 'admin_enable', 'Yes'),
                            'setting' => 'email_admin'
                        ),
                        array(
                            'key' => 'admin_mail_to',
                            'label' => __('Mail To', 'shiptrack'),
                            'type' => 'select',
                            'required' => true,
                            'class' => 'selectize',
                            'options' => $shiptrack->get_setting('email_admin', 'admin_mail_to', array($admin_default_email)),
                            'value' => $shiptrack->get_setting('email_admin', 'admin_mail_to', $admin_default_email),
                            'placeholder' => 'sample@gmail.com',
                            'description' => '<strong>Note:</strong> Type and select to add new item',
                            'extras' => 'multiple data-has_remove="true" data-allow_create="true"',
                            'setting' => 'email_admin',
                        ),
                        array(
                            'key' => 'admin_cc',
                            'label' => __('Cc', 'shiptrack'),
                            'type' => 'select',
                            'required' => false,
                            'class' => 'selectize',
                            'options' => $shiptrack->get_setting('email_admin', 'admin_cc',  array()),
                            'value' => $shiptrack->get_setting('email_admin', 'admin_cc'),
                            'placeholder' => 'sample@gmail.com',
                            'description' => '<strong>Note:</strong> Type and select to add new item',
                            'extras' => 'multiple data-has_remove="true" data-allow_create="true"',
                            'setting' => 'email_admin',
                        ),
                        array(
                            'key' => 'admin_bcc',
                            'label' => __('Bcc', 'shiptrack'),
                            'type' => 'select',
                            'required' => false,
                            'class' => 'selectize',
                            'options' => $shiptrack->get_setting('email_admin', 'admin_bcc',  array()),
                            'value' => $shiptrack->get_setting('email_admin', 'admin_bcc'),
                            'description' => '<strong>Note:</strong> Type and select to add new item',
                            'placeholder' => 'sample@gmail.com',
                            'extras' => 'multiple data-has_remove="true" data-allow_create="true"',
                            'setting' => 'email_admin',
                        ),
                        array(
                            'key' => 'admin_subject',
                            'label' => __('Subject', 'shiptrack'),
                            'type' => 'text',
                            'required' => true,
                            'class' => 'form-control',
                            'value' => $shiptrack->get_setting('email_admin', 'admin_subject', 'New Booking'),     
                            'placeholder' => 'New Booking',                       
                            'setting' => 'email_admin',
                        ),
                        array(
                            'key' => 'admin_body',
                            'label' => __('Body', 'shiptrack'),
                            'type' => 'textarea',
                            'required' => true,
                            'class' => 'form-control',
                            'value' => $shiptrack->get_setting_html('email_admin', 'admin_body', ''),     
                            'placeholder' => wpst_get_default_admin_mail_body(),                       
                            'extras' => 'rows=6',
                            'allow_html' => true,
                            'setting' => 'email_admin',
                        ),
                        array(
                            'key' => 'admin_footer',
                            'label' => __('Footer', 'shiptrack'),
                            'type' => 'textarea',
                            'required' => true,
                            'class' => 'form-control',
                            'value' => $shiptrack->get_setting_html('email_admin', 'admin_footer'),
                            'placeholder' => wpst_get_default_admin_mail_footer(),
                            'extras' => 'rows=4',
                            'allow_html' => true,
                            'setting' => 'email_admin',
                        )
                    )
                )
            ),
            'email_client' => array(
                array(
                    'heading' => esc_html__('Client Email Setting', 'shiptrack'),
                    'fields' => array(
                        array(
                            'key' => 'client_enable',
                            'label' => __('Enable?', 'shiptrack'),
                            'type' => 'radio',
                            'required' => true,
                            'class' => '',
                            'group_class' => 'form-check-inline',
                            'options' => array('Yes', 'No'),
                            'value' => $shiptrack->get_setting('email_client', 'client_enable', 'Yes'),
                            'setting' => 'email_client'
                        ),
                        array(
                            'key' => 'enabled_statuses',
                            'label' => esc_html__('Send when status:', 'wpcb_booking'),
                            'type' => 'checkbox',
                            'options' => $status_list,
                            'value' => wpst_send_client_email_in_status_list(),
                            'setting' => 'email_client',
                            'group_class' => 'form-check-inline'
                        ),
                        array(
                            'key' => 'client_mail_to',
                            'label' => __('Mail To', 'shiptrack'),
                            'type' => 'select',
                            'required' => true,
                            'class' => 'selectize',
                            'options' => $shiptrack->get_setting('email_client', 'client_mail_to', array('{wpst_shipper_email}')),
                            'value' => $shiptrack->get_setting('email_client', 'client_mail_to', '{wpst_shipper_email}'),
                            'placeholder' => 'sample@gmail.com',
                            'description' => '<strong>Note:</strong> Type and select to add new item',
                            'extras' => 'multiple data-has_remove="true" data-allow_create="true"',
                            'setting' => 'email_client',
                        ),
                        array(
                            'key' => 'client_cc',
                            'label' => __('Cc', 'shiptrack'),
                            'type' => 'select',
                            'required' => false,
                            'class' => 'selectize',
                            'options' => $shiptrack->get_setting('email_client', 'client_cc', array()),
                            'value' => $shiptrack->get_setting('email_client', 'client_cc'),
                            'placeholder' => 'sample@gmail.com',
                            'description' => '<strong>Note:</strong> Type and select to add new item',
                            'extras' => 'multiple data-has_remove="true" data-allow_create="true"',
                            'setting' => 'email_client',
                        ),
                        array(
                            'key' => 'client_bcc',
                            'label' => __('Bcc', 'shiptrack'),
                            'type' => 'select',
                            'required' => false,
                            'class' => 'selectize',
                            'options' => $shiptrack->get_setting('email_client', 'client_bcc', array()),
                            'value' => $shiptrack->get_setting('email_client', 'client_bcc'),
                            'description' => '<strong>Note:</strong> Type and select to add new item',
                            'placeholder' => 'sample@gmail.com',
                            'extras' => 'multiple data-has_remove="true" data-allow_create="true"',
                            'setting' => 'email_client',
                        ),
                        array(
                            'key' => 'client_subject',
                            'label' => __('Subject', 'shiptrack'),
                            'type' => 'text',
                            'required' => true,
                            'class' => 'form-control',
                            'value' => $shiptrack->get_setting('email_client', 'client_subject', 'Shipment Tracking No. #{shiptrack_shipment_no}'),     
                            'placeholder' => 'Shipment Tracking No. #{shiptrack_shipment_no}',                       
                            'setting' => 'email_client',
                        ),
                        array(
                            'key' => 'client_body',
                            'label' => __('Body', 'shiptrack'),
                            'type' => 'textarea',
                            'required' => true,
                            'class' => 'form-control',
                            'value' => $shiptrack->get_setting_html('email_client', 'client_body'),     
                            'placeholder' => wpst_get_default_client_mail_body(),                       
                            'extras' => 'rows=6',
                            'allow_html' => true,
                            'setting' => 'email_client',
                        ),
                        array(
                            'key' => 'client_footer',
                            'label' => __('Footer', 'shiptrack'),
                            'type' => 'textarea',
                            'required' => true,
                            'class' => 'form-control',
                            'value' => $shiptrack->get_setting_html('email_client', 'client_footer'),
                            'placeholder' => wpst_get_default_client_mail_footer(),
                            'extras' => 'rows=4',
                            'allow_html' => true,
                            'setting' => 'email_client',
                        )
                    )
                )
            ),
        );
        return apply_filters('wpst_settings_field', $fields);
    }

    function multiple_package() {
        global $shiptrack;
        $fields = array(
            'qty' => array(
                'key' => 'qty',
                'label' => __('Qty', 'shiptrack'),
                'type' => 'number',
                'class' => 'qty',
                'unit' => 'pcs',
                'order' => 1
            ),
            'package_type' => array(
                'key' => 'package_type',
                'label' => __('Package Type', 'shiptrack'),
                'type' => 'select',
                'options' => $shiptrack->package_types(),
                'field_col' => '',
                'class' => 'package_type',
                'order' => 2
            ),
            'weight' => array(
                'key' => 'weight',
                'label' => __('Weight', 'shiptrack'),
                'type' => 'number',
                'extras' => 'step=any min=0',
                'class' => 'weight',
                'unit' => $shiptrack->weight_unit_used(),
                'order' => 3
            ),
            'length' => array(
                'key' => 'length',
                'label' => __('Length', 'shiptrack'),
                'type' => 'number',
                'extras' => 'step=any min=0',
                'class' => 'length',
                'unit' => $shiptrack->dim_unit_used(),
                'order' => 4
            ),
            'width' => array(
                'key' => 'width',
                'label' => __('Width', 'shiptrack'),
                'type' => 'number',
                'extras' => 'step=any min=0',
                'class' => 'width',
                'unit' => $shiptrack->dim_unit_used(),
                'order' => 5
            ),
            'height' => array(
                'key' => 'height',
                'label' => __('Height', 'shiptrack'),
                'type' => 'number',
                'extras' => 'step=any min=0',
                'class' => 'height',
                'unit' => $shiptrack->dim_unit_used(),
                'order' => 6
            )
        );
        $fields = apply_filters('wpst_multiple_package', $fields);
        if (!empty($fields)) {
            foreach ($fields as $key => $field) {
                if (!array_key_exists('order', $field)) {
                    $field['order'] = 0;
                }
                $fields[$key] = $field;
            }
            
            $orders = array_column($fields, 'order');
            array_multisort($orders, SORT_ASC, $fields);
        }
        
        return $fields;
    }

    function history_fields($shipment_id=0, $side_bar=false) {
        global $shiptrack;
        $status_label = $side_bar ? __('New Status', 'shiptrack') : __('Status', 'shiptrack');
        $fields = array(
            'shiptrack_status' => array(
                'key' => 'shiptrack_status',
                'label' => $status_label,
                'type' => 'select',
                'options' => $shiptrack->status_list(),
                'value' => !$shipment_id ? wpst_get_default_status() : '',
            ),
            'shiptrack_datetime' => array(
                'key' => 'shiptrack_datetime',
                'label' => __('Date Time', 'shiptrack'),
                'type' => 'text',
                'class' => 'wpst-datetimepicker',
                'value' => '',
            ),
            'remarks' => array(
                'key' => 'remarks',
                'label' => __('Remarks', 'shiptrack'),
                'type' => 'textarea',
                'value' => '',
                'extras' => 'rows=auto'
            ),
            'updated_by' => array(
                'key' => 'updated_by',
                'label' => __('Updated By', 'shiptrack'),
                'type' => 'text',
                'value' => '',
                'extras' => 'readonly'
            ),
        );
        if ($side_bar) {
            unset($fields['shiptrack_datetime']);
            unset($fields['updated_by']);
        }
        return apply_filters('wpst_history_fields', $fields);
    }

    function shipment_list_columns() {
        $columns = array(
            'checkbox' => array(
                'key'=>'checkbox',
                'label' => '<input class="form-check-input m-0 select-all" type="checkbox" id="check-all"/>',
                'class' => 'cb-item'
            ),
            'tracking_no' => array(
                'key' => 'tracking_no',
                'label' => 'Tracking No',
                'class' => 'tracking-no',
                'extras' => 'shipment-{shipment_id}'
            ),
            'shipper' => array(
                'key' => wpst_customer_field('shipper', 'key'),
                'label' => wpst_customer_field('shipper', 'label'),
                'class' => 'shipper',
            ),
            'receiver' => array(
                'key' => wpst_customer_field('receiver', 'key'),
                'label' => wpst_customer_field('receiver', 'label'),
                'class' => 'receiver',
            ),
            'shiptrack_status' => array(
                'key' => 'shiptrack_status',
                'label' => 'Status',
                'class' => 'status'
            ),
            'date_created' => array(
                'key' => 'date_created',
                'type' => 'date',
                'label' => 'Date Created',
                'class' => 'date-created'
            ),
        );
        return apply_filters('wpst_shipment_list_columns', $columns);
    }
}

$WPSTField = new WPSTField;