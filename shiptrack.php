<?php
/*
 * Plugin Name: ShipTrack - Shipments
 * Description: Shiptract is designed to provide information to your shippers and consignees, technology solution for shipments and logistics operations. It has core features to manage shipments in easy way, printing of shipment's documents, track shipments and notify your customers about their shipment status.
 * Author: <a href="https://join.skype.com/invite/yT6ad4cNTTJM">shiptrack</a>
 * Text Domain: shiptrack
 * Domain Path: /languages
 * Version: 1.0.0
 */

 /** 
  * ShipTrack - Shipments
  * Copyright (C) 2022  shiptrack
  */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/** Defined constant */
define('WPST_TEXTDOMAIN', 'shiptrack');
define('WPST_VERSION', '1.0.0');
define('WPST_DB_VERSION', '1.0.0');
define('WPST_FILE_DIR', __FILE__);
define('WPST_PLUGIN_URL', plugin_dir_url(WPST_FILE_DIR));
define('WPST_PLUGIN_PATH', plugin_dir_path(WPST_FILE_DIR));

/** enqueue scipts */
require_once WPST_PLUGIN_PATH. 'includes/labels.php';
require_once WPST_PLUGIN_PATH. 'includes/encryption.php';
require_once WPST_PLUGIN_PATH. 'includes/functions.php';
require_once WPST_PLUGIN_PATH. 'classes/PostType.php';
require_once WPST_PLUGIN_PATH. 'classes/Country.php';
require_once WPST_PLUGIN_PATH. 'classes/Load.php';
require_once WPST_PLUGIN_PATH. 'classes/Form.php';
require_once WPST_PLUGIN_PATH. 'classes/Menu.php';
require_once WPST_PLUGIN_PATH. 'classes/Hook.php';
require_once WPST_PLUGIN_PATH. 'classes/Field.php';
require_once WPST_PLUGIN_PATH. 'classes/Ajax.php';
require_once WPST_PLUGIN_PATH. 'classes/ShipTrack.php';
require_once WPST_PLUGIN_PATH. 'classes/Asset.php';

if (is_admin()) {
  require_once WPST_PLUGIN_PATH. 'classes/Activation.php';
}

/** Load text Domain */
add_action('plugins_loaded', array('WPSTLoad','wpst_load_textdomain'));

/** Create Tracking Page */
register_activation_hook(WPST_FILE_DIR, array( 'WPSTLoad', 'wpst_create_default_page'));