<?php

namespace SodTrack;

class Admin_Widget {
    
    public function __construct() {
        if (is_admin()) {
            add_action( 'wp_dashboard_setup', array( $this,  'dashboard'));
        }
    }

    public function dashboard() {
        wp_add_dashboard_widget( 'dashboard_widget', 'User Search Track', array( $this, 'dashboard_widget_function') );
    }
   
    public function dashboard_widget_function() {
        $res = sodtrack()->track->get_tracks();
        if (!empty($res)):
        ?>
        <table class="wp-list-table widefat table-view-list">
            <thead>
                <tr>
                    <!-- <th>
                        ID
                    </th> -->
                    <th>
                        Search
                    </th>
                    <th>
                        Category
                    </th>
                    <!-- <th>
                        Address
                    </th>
                    <th>
                        Tags
                    </th>
                    <th>
                        Count
                    </th> -->
                    <th>
                        Created At
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                    foreach ($res as $r):
                ?>
                <tr>
                    <!-- <td>
                        <?php echo ++$i; ?>
                    </td> -->
                    <td>
                        <?php echo $r->query; ?>
                    </td>
                    <td>
                        <?php echo $r->categories; ?>
                    </td>
                    <!-- <td>
                        <?php echo $r->addresses; ?>
                    </td>
                    <td>
                        <?php echo $r->tags; ?>
                    </td>
                    <td>
                        <?php echo $r->query_count; ?>
                    </td> -->
                    <td>
                        <?php 
                         echo sod_time_ago(strtotime($r->created_at)); ?>
                    </td>
                </tr>
                <?php
                    endforeach;
                ?>
            </tbody>
        </table>
       
        <?php
        else:
            echo "No Track Data";
        endif;
        ?>
        <p> <a href="<?php echo admin_url("options-general.php?page=sod-track-search"); ?>">See more details</a></p>
        <?php
    }
}