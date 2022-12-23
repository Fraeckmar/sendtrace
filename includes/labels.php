<?php

function wpst_label() {
    return esc_html(apply_filters('wpst_label', __('ShipTrack', 'shiptrack')));
}

function wpst_shipments_label() {
    return esc_html(apply_filters('wpst_shipments_label', __('Shipments', 'shiptrack')));
}

function wpst_shipment_label() {
    return esc_html(apply_filters('wpst_shipment_label', __('Shipment', 'shiptrack')));
}

function wpst_new_shipment_label() {
    return esc_html(apply_filters('wpst_new_shipment_label', __('New Shipment', 'shiptrack')));
}

function wpst_edit_shipment_label() {
    return esc_html(apply_filters('wpst_edit_shipment_label', __('Edit Shipment', 'shiptrack')));
}

function wpst_multiple_package_label() {
    return esc_html(apply_filters('wpst_multiple_package_label', __('Packages', 'shiptrack')));
}

function wpst_history_label() {
    return esc_html(apply_filters('wpst_history_label', __('History', 'shiptrack')));
}

function wpst_settings_label() {
    return esc_html(apply_filters('wpst_settings_label', __('Settings', 'shiptrack')));
}

function wpst_volumetric_weight_label() {
    return esc_html(apply_filters('wpst_volumetric_weight_label', __('Volumetric Weight', 'shiptrack')));
}

function wpst_actuual_weight_label() {
    return esc_html(apply_filters('wpst_actuual_weight_label', __('Actual Weight', 'shiptrack')));
}

function wpst_cubic_unit_label($unit='meter') {
    $cubic_unit = 'Cubic '.ucwords($unit);
    return esc_html(apply_filters('wpst_cubic_unit_label', $cubic_unit, $unit));
}

function wpst_track_button_label()
{
    return  apply_filters('track_button_label', __('Track', 'shiptrack'));
}