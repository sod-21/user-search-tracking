<?php

namespace memberpress\sod;

if (!defined('ABSPATH')) {
    exit;
}

use \WP_List_Table;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class DataCollection extends WP_List_Table
{
    /**
     * Table title
     */

    public $title = "";

    /**
     * headers
     */
    public $headers = [];

    /**
     * rows
     */
    public $rows = [];

    public function __construct()
    {
        parent::__construct();

        $data = Helper::get_data();

        if (!empty($data)) {
            $this->title = isset($data->title) ? $data->title : "";
            $this->headers = isset($data->data->headers) ? $data->data->headers  : [];
            $this->rows = isset($data->data->rows) ? $data->data->rows : [];
        }
    }

    function get_columns()
    {
        $columns = [];

        foreach ($this->headers as $col) {
            $columns[$col] = $col;
        }

        return $columns;
    }

    function prepare_items()
    {

        $columns = $this->get_columns();

        $this->_column_headers = array($columns);
        $this->process_bluk_action();
    }

    function column_default($item, $column_name)
    {

        switch ($column_name) {
            case 'ID':
                return $item->id;
            case 'First Name':
                return $item->fname;
            case 'Last Name':
                return $item->lname;
            case 'Email':
                return $item->email;
            case 'Date':
                return date("Y-m-d H:i:s", $item->date);
        }
    }

    function process_bluk_action()
    {
        $this->items = $this->rows;
    }
}

?>

<div class="wrap">
    <?php
    $table = new DataCollection();

    echo "<h2>$table->title</h2>";

    $table->prepare_items();
    $table->display();
    ?>
</div>