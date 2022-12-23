<?php

class WPSTActivation
{
    function __construct()
    {
        add_action('activated_plugin', array($this, 'add_roles'));
    }
    public function add_roles()
    {
        // Client
        $client_role = get_role('shiptrack_client');
        if (!$client_role) {
            add_role('shiptrack_client', 'Shiptrack Client', array(
                'read' => true,
            ));
        }
        
        // Editor
        $editor_role = get_role('shiptrack_editor');
        if (!$editor_role) {
            add_role('shiptrack_editor', 'Shiptrack Editor', array(
                'read' => true,
                'create_posts' => true,
                'edit_posts' => true,
                'edit_others_posts' => true,
                'edit_published_posts' => true,
                'delete_posts' => false
            ));
        }     
        
        // Agent
        $editor_role = get_role('shiptrack_agent');
        if (!$editor_role) {
            add_role('shiptrack_agent', 'Shiptrack Agent', array(
                'read' => true,
                'create_posts' => true,
                'edit_posts' => true,
                'edit_others_posts' => true,
                'edit_published_posts' => true,
                'delete_posts' => false
            ));
        }     
    }
}
new WPSTActivation;