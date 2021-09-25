<?php

namespace memberpress\sod;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * admin options
 * 
 */
final class Admin
{

    /**
     * admin slug
     */

    const ADMIN_SLUG = "memberpress_sod";

    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('in_admin_header', array($this, 'admin_header'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('init', array($this, 'refresh_data'));
    }

    /**
     * add the admin menu
     * 
     */
    public function admin_menu()
    {

        global $submenu;

        add_menu_page(
            esc_html__('MemberPress Sod Challenge', 'memberpress-sod-cha'),
            esc_html__('MemberPress Sod Challenge', 'memberpress-sod-cha'),
            'manage_options',
            self::ADMIN_SLUG,
            [$this, 'memberpress_sod_data'],
            MEMBERPRESS_SOD_CHA_URL . '/assets/img/memberpress.png'
        );

        // settings
        add_submenu_page(
            self::ADMIN_SLUG,
            esc_html('settings',  'memberpress-sod-cha'),
            esc_html('Settings',  'memberpress-sod-cha'),
            'manage_options',
            'memberpress_sod_settings',
            [$this, 'memberpress_sod_settings']
        );
    }

    /**
     * show settings page
     */
    public function memberpress_sod_settings()
    {
        require_once(MEMBERPRESS_SOD_CHA_PATH . "/includes/partials/settings.php");
    }

    /**
     * show data list
     * 
     */
    public function memberpress_sod_data()
    {
        require_once(MEMBERPRESS_SOD_CHA_PATH . "/includes/partials/list.php");
    }

    /**
     * add admin_header
     */
    public function admin_header()
    {
        require_once(MEMBERPRESS_SOD_CHA_PATH . "/includes/partials/admin_header.php");
    }

    /**
     * add the style
     */
    public function enqueue_scripts()
    {

        // add the style
        wp_enqueue_style('memberpress-sod-admin-style', MEMBERPRESS_SOD_CHA_URL . "/assets/css/admin.css", null,  MEMBERPRESS_SOD_CHA_VERSION);
    }

    /**
     * refresh the data
     */
    public function refresh_data()
    {

        if (
            is_admin() &&
            isset($_REQUEST["page"]) &&
            $_REQUEST["page"] == "memberpress_sod_settings" &&
            isset($_REQUEST["refresh_api"])
        ) {
            $data = Helper::get_data(true);

            if (!empty($data)) {

                wp_safe_redirect(admin_url("admin.php?page=memberpress_sod_settings#success"));
            }
        }
    }
}
