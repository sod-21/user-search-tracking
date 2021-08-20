<?php

namespace SodTrack;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SodTrack {
    private static $instance = null;
    public $track;
    public $widget;

    public function __construct() {
        $this->include_files();
        $this->track = new Track();
        $this->widget = new Admin_Widget();

        add_action('admin_menu', array( $this, 'add_admin_page') );
    }

    private function include_files() {
        require_once SOD_TRACK_PATH . '/includes/functions.php';
		require_once SOD_TRACK_PATH . '/includes/track.php';
        require_once SOD_TRACK_PATH . '/includes/admin_widget.php';
	}    

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new SodTrack();
        }

        return self::$instance;
    }

    public function add_admin_page() {
        add_submenu_page('index.php', __('Search Tracking', 'sod-track-search'), __('Search Tracking', 'sod-track-search'), null, 'sod-track-search', array( $this,  'options_page'));
        add_options_page(__('Search Tracking', 'sod-track-search'), __('Search Tracking', 'sod-track-search'), 'manage_options', 'sod-track-search', array( $this,  'options_page'));
    }

    public function options_page() {

        $table = new TrackList();
      
        ?>
        <div class="wrap">
        <style type="text/css">
            #wpse-list-table-form table > tfoot {
                display: none;
            }
        </style>
        <h2><?php _e('Search Tracking Settings', 'search-meter') ?></h2>
        
        <?php
        echo '<form id="wpse-list-table-form" method="post">';

        $page  = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED );
        $paged = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );
        
        printf( '<input type="hidden" name="page" value="%s" />', $page );
        printf( '<input type="hidden" name="paged" value="%d" />', $paged );
        
        $table->prepare_items(); // this will prepare the items AND process the bulk actions
        $table->display();
        
        echo '</form>';
    }
}
