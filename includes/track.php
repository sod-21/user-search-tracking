<?php

namespace SodTrack;

use \WP_List_Table;

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Track {
    const table = "mittun_track";
    public const per_page = 20;

    public function __construct() {
        add_action( 'init', array( $this,  'track'));
    }

    public function track() {
        $search = isset($_REQUEST["q"]) ? esc_sql($_REQUEST["q"]) : "";
        $in_cat = isset($_REQUEST["in_cat"]) ? esc_sql($_REQUEST["in_cat"]) : "";
        $address = isset($_REQUEST["address"]) ? esc_sql($_REQUEST["address"]) : "";
        $in_tag = isset($_REQUEST["in_tag"]) ? esc_sql($_REQUEST["in_tag"]) : "";
        $custom_fields = isset($_REQUEST["custom_field"]) ? $_REQUEST["custom_field"] : [];
        
        if ( isset($_REQUEST["q"]) && isset($_REQUEST["in_cat"]) && isset($_REQUEST["address"])) {
            $custom_field = "";
            if (is_array($custom_fields) && !empty( $custom_fields)) {
                $custom_field =  esc_sql(implode(",", array_values($custom_fields)));
            }

            if ($search || $in_cat || $address || $in_tag || $custom_field) {
                if ($in_cat && is_numeric($in_cat)) {
                    $term = get_term($in_cat);
                    $in_cat = $term->name;
                }

                if ($in_tag && is_numeric($in_tag)) {
                    $term = get_term($in_tag);
                    $in_tag = $term->name;
                }

                foreach ( $custom_fields as $cf) {
                    $this->save_track($search, $in_cat, $address, $in_tag, esc_sql($cf));
                }
                
                if (!$custom_field) {
                    $this->save_track($search, $in_cat, $address, $in_tag, $custom_field);
                }
                            }
        }

        if (isset($_POST["sod-track-export"])) {
            $type = isset($_POST["query"]) ? $_POST["query"] : "";
            $term = isset($_REQUEST["term"]) ? $_REQUEST["term"] : "week";
            $this->export_csv($type, $term);
            exit;
        }
    }

    public static function current_url()
    {
        if (isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public function save_track($search, $in_cat, $address, $in_tag, $custom_field) {
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . self::table;
            $user_id = get_current_user_id();
            $page_url = self::current_url();

            $sql = "INSERT INTO $table_name(`query`, `category`, `address`, `tag`, `custom_field`, `user_id`, `url`) VALUES('$search', '$in_cat', '$address', '$in_tag', '$custom_field', '$user_id', '$page_url' )";
            
            $res = $wpdb->query($sql);
            return $res;
        }
        catch (Exception $e) {
            return false;
        }
        
    }

    public function get_tracks() {
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . self::table;

            $sql = "SELECT COUNT(query) as query_count, GROUP_CONCAT(DISTINCT category) as categories, MAX(created_at) as created_at, query FROM $table_name GROUP BY query  ORDER BY created_at DESC LIMIT 10";
            $res = $wpdb->get_results($sql);

            return $res;
        }

        catch (Exception $e) {
            return false;
        }
    }

    public function get_tracks_by_search($type = 'query', $limit = 1, $term="week") {
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . self::table;
            $sql = "";
            if ($limit == -1) {
                $limit = "";
            } else if ($limit == 1) {
                $limit = " limit " . self::per_page;
            } else {
                $offset = ($limit - 1) * self::per_page;

                $limit = " limit $offset, " . self::per_page;
            }

            $where = " 1 = 1";
            if ($term == "day") {
                $where = " DATE_FORMAT(created_at,'%Y%c%d')= DATE_FORMAT(now(),'%Y%c%d') ";
            } else if ($term == "week") {
                $where = " date(created_at) >= DATE(NOW()) - interval 7 day  ";
            } else if ($term == "month") {
                $where = " date(created_at) >= DATE(NOW()) - interval 30 day  ";
            }

            switch ($type) {
                case 'query':                    
                    $sql = "SELECT SQL_CALC_FOUND_ROWS  COUNT(query) as total, COUNT(query) * 100/ IFNULL((SELECT COUNT(*) FROM $table_name WHERE query <> '' AND $where ), 1) as query_count,  query FROM $table_name WHERE query <> '' AND $where  GROUP BY query  ORDER BY query DESC $limit";
                   
                    break;
                    
                case 'category':
                    $sql = "SELECT SQL_CALC_FOUND_ROWS COUNT(category) as total, COUNT(category) * 100 / IFNULL((SELECT COUNT(*) FROM $table_name WHERE category <> '' AND $where ), 1) as query_count,   category as query FROM $table_name WHERE category <> '' AND $where GROUP BY category  ORDER BY category DESC $limit";
                    break;
                case 'address':
                    $sql = "SELECT SQL_CALC_FOUND_ROWS  COUNT(address) as total, COUNT(address)  * 100 / IFNULL((SELECT COUNT(*) FROM $table_name WHERE address <> '' AND $where ), 1) as query_count,  address as query FROM $table_name WHERE address <> '' AND $where GROUP BY address  ORDER BY address DESC $limit";
                    break;
                case 'tag':
                    $sql = "SELECT SQL_CALC_FOUND_ROWS COUNT(tag) as total, COUNT(tag)  * 100 / IFNULL((SELECT COUNT(*) FROM $table_name WHERE tag <> '' AND $where ), 1) as query_count,    tag as query FROM $table_name WHERE tag <> '' AND $where GROUP BY tag  ORDER BY tag DESC   $limit";
                    break;
                case 'custom_field':
                    $sql = "SELECT SQL_CALC_FOUND_ROWS COUNT(custom_field) as total, COUNT(custom_field) * 100 / IFNULL((SELECT COUNT(*) FROM $table_name WHERE custom_field <> '' AND $where ), 1) as query_count,    custom_field as query FROM $table_name WHERE custom_field <> '' AND $where  GROUP BY custom_field  ORDER BY custom_field DESC   $limit";
                    break;
                case 'user':
                    $sql = "SELECT SQL_CALC_FOUND_ROWS COUNT(IFNULL(user_id, 0)) as total, COUNT(IFNULL(user_id, 0)) * 100 / IFNULL((SELECT COUNT(*) FROM $table_name WHERE $where ), 1) as query_count, IFNULL(user_id, 0) as query FROM $table_name WHERE $where  GROUP BY user_id  ORDER BY user_id DESC   $limit";                    
                    break;
                default:
                    $sql = "SELECT SQL_CALC_FOUND_ROWS  COUNT(query) as total, COUNT(query)  * 100/ IFNULL((SELECT COUNT(*) FROM $table_name WHERE query <> '' AND $where ), 1) as query_count,  query FROM $table_name WHERE query <> '' AND $where GROUP BY query  ORDER BY query DESC $limit";
                    break;
            }
            
            $res = $wpdb->get_results($sql);
            $total = $wpdb->get_var("SELECT FOUND_ROWS()");
            return array("items" => $res, "total" => $total);
        }

        catch (Exception $e) {
            return false;
        }
    }


    public function export_csv($type, $term) {
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . self::table;
            $sql = "";

            $limit = "";
            $results = $this->get_tracks_by_search($type, -1, $term);

            if ($results) {
                $res = $results["items"];
            }

            if (!empty($res)) {
                $filename = "$type-$term-" . date('Y-m-d') . ".csv";
                $delimiter = ",";

                $f = fopen('php://memory', 'w');

                $fields = array( 'Search Terms', 'Total Searches',  '% of Searches');
                fputcsv( $f, $fields, $delimiter);

                foreach ($res as $r) {
                    fputcsv( $f, [$r->query, $r->total, $r->query_count], $delimiter);
                }

                fseek ($f, 0);
                header('Content-Type: text/csv'); 
                header('Content-Disposition: attachment; filename="' . $filename . '";'); 
                
                //output all remaining data on a file pointer 
                fpassthru($f);
                exit;
            }
        }

        catch (Exception $e) {
            return false;
        }
    }

    public static function create_table() {
        global $wpdb;		
		$dbTable = $wpdb->prefix . self::table;
		
		$table_track = "CREATE TABLE IF NOT EXISTS `" . $dbTable . "` (
		`id` int (11) NOT NULL AUTO_INCREMENT,
		`query` VARCHAR(255),
        `category` VARCHAR(50),
        `address` VARCHAR(50),        
        `tag` VARCHAR(50),        
        `custom_field` VARCHAR(255),
        `url` VARCHAR(255),
        `user_id` int(11),        
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,	
		PRIMARY KEY(`id`),
        INDEX `track` (`created_at`)
		) " .$wpdb->get_charset_collate()."";

		if (!function_exists('dbDelta')) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    	}

	    dbDelta($table_track);
    
    }
}

class TrackList extends WP_List_Table {

    function get_columns(){
        $columns = array(           
            'Search Terms'    => __('Search Terms', 'sod-track-search'),            
            'Total Searches'    => __('Total Searches', 'sod-track-search'), 
            'Percentage' => __('% of Searches', 'sod-track-search'),            
        );

        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'Search Terms' => array('Search Terms', true),
            'Total Searches'    => __('Total Searches', 'sod-track-search'), 
            'Percentage' => array('% of Searches', true),
        );

        return $sortable_columns;
    }

    function prepare_items() {
     
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bluk_action();
    }

    public function get_bulk_actions()
    {
        // return array(
        //     'query' => __('Search', 'sod-track-search'),
        //     'category' => __('Category', 'sod-track-search'),
        //     'address' => __('Address', 'sod-track-search'),
        //     'tag' => __('Tags', 'sod-track-search'),
        //     'custom_field' => __('Custom Field', 'sod-track-search'),
        //     'user' => __('Users', 'sod-track-search')
        // );
        return [];
    }

    function column_default( $item, $column_name ) {
       
        switch( $column_name ) {
            case 'Search Terms':                
               return $item->query;
            case 'Total Searches':
                return $item->total;
            case 'Percentage':
                return number_format($item->query_count, 2, ".", ",") . "%";
        }
    }
    
    protected function extra_tablenav( $which ) {
        if ($which === "bottom")    {
            return;
        }
    
        $id = 'bottom' === $which ? 'query2' : 'query';
        $term_id = 'term';
        $term = isset($_REQUEST[$term_id]) ? $_REQUEST[$term_id] : "";
        $query = isset($_REQUEST[$id]) ? $_REQUEST[$id] : "";
        
        $types =  array(
            'query' => __('Search', 'sod-track-search'),
            'category' => __('Category', 'sod-track-search'),
            'address' => __('Address', 'sod-track-search'),
            'tag' => __('Tags', 'sod-track-search'),
            'custom_field' => __('Custom Field', 'sod-track-search'),
            'user' => __('Users', 'sod-track-search')
        );

        $terms = array(
            'day' => 'Today',
            'week' => 'Last a week',
            'month' => 'Last a month',
            'all' => 'All'
        );
		?>
	    <div class="alignleft actions">
		
		<label class="screen-reader-text" for="<?php echo $id; ?>"><?php _e( 'Search Terms' ); ?></label>
		<select name="<?php echo $id; ?>" id="<?php echo $id; ?>">			
			<?php 
            foreach ($types as $key => $t) {
                if ($key == $query) {
                    echo "<option value='$key' selected='selected'>$t</option>";
                } else {
                    echo "<option value='$key'>$t</option>";
                }
            }
            ?>			
		</select>

        <select name="<?php echo $term_id; ?>" id="<?php echo $term_id; ?>">	
            echo "<option value='week'><?php echo "Select a period"; ?></option>";		
			<?php 
            foreach ($terms as $key => $t) {
                if ($key == $term) {
                    echo "<option value='$key' selected='selected'>$t</option>";
                } else {
                    echo "<option value='$key'>$t</option>";
                }
            }
            ?>			
		</select>

			<?php
			submit_button( __( 'Change' ), '', 'search', false );
        ?>
        </div>
        <div class="alignleft actions">
        <?php
			submit_button( __( 'Export' ), 'export button-primary', 'sod-track-export', false );
        ?>
        </div>
        <?php
	}

    function process_bluk_action() {
        $action = isset($_REQUEST["query"]) ? $_REQUEST["query"] : "";
        $per_page = Track::per_page;
        $paged = isset($_REQUEST["paged"]) ? $_REQUEST["paged"] : 1;
        $term = isset($_REQUEST["term"]) ? $_REQUEST["term"] : "week";

        if (isset($_REQUEST["query2"])) {
            $action = $_REQUEST["query2"];            
        }

        switch ($action) {
            case 'query':                
                $results = sodtrack()->track->get_tracks_by_search('query', $paged, $term);                
                break;
            case 'category':
                $results = sodtrack()->track->get_tracks_by_search('category', $paged, $term);
                break;
            case 'address':
                $results = sodtrack()->track->get_tracks_by_search('address', $paged, $term);
                break;
            case 'tag':
                $results = sodtrack()->track->get_tracks_by_search('tag', $paged, $term);
                break;
            case 'custom_field':
                $results = sodtrack()->track->get_tracks_by_search('custom_field', $paged, $term);
                break;
            case 'user':
                $results = sodtrack()->track->get_tracks_by_search('user', $paged, $term);
                break;
            default: 
                $results = sodtrack()->track->get_tracks_by_search('query', $paged, $term);
            break;
        }

        
                
        $items= $results["items"];
        usort($items, function($a, $b) {
            $orderby = (!empty($_REQUEST["orderby"])) ? $_REQUEST["orderby"] : "Search Terms";
            $order = (!empty($_REQUEST["order"])) ? $_REQUEST["order"] : "asc";

            if ($orderby == "Search Terms") {
                if ($order == "asc") {                  
                    return $a->query > $b->query;
                } else {
                    return $a->query <= $b->query;
                }
            } else if ($orderby == "Total Searches" || $orderby == "Percentage") {
                if ($order == "asc") {
                    return $a->query_count - $b->query_count;
                } else {
                    return $b->query_count - $a->query_count;
                }
            }
        });

        $this->items = $items;
        $total_team =  $results["total"];
        $this->set_pagination_args( array(
            'total_items' => $total_team,                
            'per_page'    => $per_page 
        ) );
    }


}

?>