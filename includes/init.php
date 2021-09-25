<?php

namespace memberpress\sod;

use \WP_CLI;

if (!defined('ABSPATH')) {
    exit;
}

final class Init
{
    /**
     * nonce
     */
    const NONCE_FOR_DATA = "Memberpress #@#$@#####$@#!SADF";

    public function __construct()
    {
        $this->include_files();
        $this->add_actions();
    }

    public function include_files()
    {

        require_once MEMBERPRESS_SOD_CHA_PATH . '/includes/helper.php';

        if (is_admin()) {
            require_once MEMBERPRESS_SOD_CHA_PATH . '/includes/admin.php';

            new Admin();
        }
    }

    /**
     * add actions
     */
    public function add_actions()
    {
        add_action('cli_init', array($this, 'add_cli'));
        add_action('wp_enqueue_scripts', array($this, 'add_scripts_styles'));

        //wp ajax to get the data from API
        add_action('wp_ajax_memberpress_sod_data', array($this, 'ajax_data'));
        add_action('wp_ajax_nopriv_memberpress_sod_data', array($this, 'ajax_data'));

        //add_shortcode
        add_shortcode('memberpress_sod_challenge', array($this, 'get_data_shortcode'));
    }

    /**
     * add styles and scripts
     * register datatable and custom js, css
     */
    public function add_scripts_styles()
    {
        wp_register_script('memberpress-sod-datatable', 'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js', array('jquery'), MEMBERPRESS_SOD_CHA_VERSION, true);

        wp_register_script('memberpress-sod-js', MEMBERPRESS_SOD_CHA_URL . "/assets/js/script.js", array('jquery', 'memberpress-sod-datatable'), MEMBERPRESS_SOD_CHA_VERSION, true);

        wp_register_style(
            'memberpress-sod-datatble',
            "//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css",
            null,
            MEMBERPRESS_SOD_CHA_VERSION
        );

        wp_register_style(
            'memberpress-sod-style',
            MEMBERPRESS_SOD_CHA_URL . "/assets/css/style.css",
            null,
            MEMBERPRESS_SOD_CHA_VERSION
        );

        wp_localize_script('memberpress-sod-js', 'memberpresssodajax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(self::NONCE_FOR_DATA)
        ));
    }

    /**
     * register the command 
     * 
     */
    public function add_cli()
    {
        WP_CLI::add_command('memberpress sod challenge refresh', array($this, 'refresh_data'));
    }

    /**
     * refreshing data
     * 
     */
    public function refresh_data()
    {
        WP_CLI::line('refresh the data form API' . Helper::API);
        WP_CLI::line('Starting...');

        $data = Helper::get_data(true);

        if (!empty($data)) {
            WP_CLI::line(__('refreshed successfully.', 'memberpress-sod-cha'));
        } else {
            WP_CLI::line(WP_CLI::colorize('%r Error:%n' . __('Please check the api key or internet.', 'memberpress-sod-cha')));
        }

        WP_CLI::line('Good Bye');
    }

    /**
     * ajax 
     */
    public function ajax_data()
    {

        if (!wp_verify_nonce($_POST["nonce"], self::NONCE_FOR_DATA)) {
            die('error');
        } else {
            $data = Helper::get_data();

            if (!empty($data)) {
                echo json_encode($data);
            }
        }
        exit;
    }

    /**
     * rendering the table
     * 
     */
    public function get_data_shortcode($atts = array(), $content = null)
    {

        extract(
            shortcode_atts(
                array(
                    'title' => '',
                    'count' => '5'
                ),
                $atts
            )
        );

        wp_enqueue_script('memberpress-sod-datatable');
        wp_enqueue_script('memberpress-sod-js');
        wp_enqueue_style('memberpress-sod-datatble');
        wp_enqueue_style('memberpress-sod-style');

        return "<div class='memberpress-sod-challenge loading'><h2>$title</h2><div class='table-wrapper' data-page-count='$count'>
        <div class='lds-roller'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><table class='table'><thead><tr></tr></thead><tbody></tbody></table></div></div>";
    }
}

new Init();
