<?php

class PostHitCount
{

    var $table = 'hitsCount';
    var $recordCount = 0;
    var $recordLimit = 100;
    var $urlString = '';

    public function counterAdmin()
    {
        $this->postHitCounterAdmin();
    }


    public function callCounter()
    {
        $this->countPostRead();
    }


    private function postHitCounterAdmin()
    {
        add_menu_page('Hits Counter', 'Hits Counter', 'manage_options', 'post_hit_counter', array($this, 'adminPHC'), 'dashicons-chart-bar', '55');
        add_submenu_page('post_hit_counter', 'Settings', 'Hits Counter Setting', 'manage_options', 'hit_counter_setting', array($this, 'settingPHC'));
    }


    public function settingPHC()
    {
        ?>
        <h1>Setting Hits Counter</h1>
        <?php
            if(isset($_POST['save_hcs']))
            {
                unset($_POST['save_hcs']);
                update_option('post_type_hits_cout_', serialize($_POST));
            }

            $setOption = unserialize(get_option('post_type_hits_cout_'));

            $args = array('public' => true, 'show_ui' => true, 'exclude_from_search' => false, 'show_in_nav_menus' => true);        
            $postTypes = get_post_types($args, 'objects', 'and');
        ?>
        <div class="">
            <form name="" action="" method="post">
                <?php
                    foreach($postTypes as $postType)
                    {
                        $checked = isset($setOption[$postType->name]) && $setOption[$postType->name] == 1 ? 'checked' : '';
                        echo '<p><input type="checkbox" name="'. $postType->name .'" '. $checked .' value="1"/>';
                        echo '<label>'. $postType->label .'</label></p>';
                    }
                ?>
                <button type="submit" name="save_hcs" class="button button-primary">Submit</button>
            </form>
        </div>
        <?php
    }


    public function adminPHC()
    {
        global $wpdb;
        wp_enqueue_script('jquery-ui-datepicker');
        ?>
        <style type="text/css">
            .post_hit_record{
                display: block;
                max-width: 98%;
            }

            .post_hit_record table{
                text-align: center;
                margin: 1% 0;
                display: inline-block;
            }
            .post_hit_record table th{
                font-weight:bold;
                text-align:center;
                padding:8px 10px;
            }
            
            .post_hit_record table td{padding:8px 10px;}

            .post_hit_record input[type="text"]{
                background: #fff;
                padding: 5px;
            }

            .ui-datepicker {
                background: #fff;
                padding: 5px;
                font-size: 12px;
            }

            .ui-datepicker td {
                padding: 3px;
            }

            a.ui-state-default {
                text-decoration: none;
                padding: 4px 3px;
                cursor: pointer;
            }

            a.ui-state-default:hover {
                background: #0073aa;
                color: #fff;
            }

            .ui-datepicker-title {
                float: left;
                cursor: pointer;
                padding: 0 5px 0;
            }

            .ui-datepicker-title select.ui-datepicker-year,
            .ui-datepicker-title select.ui-datepicker-month{
                display: inline-block;
                margin: 0;
                padding: 0px 0;
                height: 24px;
                float: left;
            }

            a.ui-datepicker-next.ui-corner-all,
            a.ui-datepicker-prev.ui-corner-all {
                padding: 3px 7px;
                background: #aaa;
                font-size: 13px;
                color: #eff;
                border-radius: 0px;
                cursor: pointer;
                float: left;
            }

            a.ui-datepicker-next.ui-corner-all {
                float: right;
            }
        </style>
        <script>
            jQuery(document).ready(function () {
                jQuery('input[name="date"]').datepicker({changeMonth: true,
                    changeYear: true, inline: false,
                    dateFormat: "yy-mm-dd"
                });
            });
        </script>
        <div class="post_hit_record">
            <h2>Post Hit Counts Record</h2>
            <div class="alignleft">
                <form method="post" action="<?= site_url() ?>/wp-admin/admin.php?page=post_hit_counter">
                    <input type="text" name="date" class="input" placeholder="Date" readonly="" value="<?php echo $this->getFromUrl('date') ?>" />
                    <input type="submit" class="button button-primary" name="find_record" value="Find Record"/>
                </form>
            </div>
            <div class="alignright">
                <a class="button" class="button" href="<?= site_url() ?>/wp-admin/admin.php?page=post_hit_counter&rcrdfnd=all_time">
                    All Time Hit Count
                </a>
            </div>
            <div class="">
            <table class="wp-list-table form-table widefat fixed striped" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Post ID</th>						
                        <th>Post Name</th>
                        <th>Date</th>
                        <th>Hit Count</th>
                        <th>View Post</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $dateRecord = date('Y-m-d');
                    if (isset($_POST['find_record']) && isset($_POST['date']))
                    {
                        $dateRecord = ($_POST['date'] != '' ? $_POST['date'] : '');
                    }

                    $table = $wpdb->prefix . $this->table;
                    $select = 'SELECT `id`, `post_id`, `hit_count`, `hit_date` FROM ' . $table . ' WHERE DATE(hit_date)="' . $dateRecord . '"';

                    if (isset($_GET['rcrdfnd']) && $_GET['rcrdfnd'] == 'all_time')
                    {
                        $select = 'SELECT `id`, `post_id`, SUM(`hit_count`) as `hit_count` FROM ' . $table . ' WHERE 1 GROUP BY `post_id` ORDER BY `hit_count` DESC';
                    }

                    /*if(isset($_GET['pg']) && $_GET['pg']!='')
                    {
                        $page = $_GET['pg'];
                        if($page > 1)
                        {
                            $select .= ' LIMIT ' . ($page - 1 * $recordLimit) . ', ' . ($page + 1 * $recordLimit);
                        }
                    }
                    else
                    {
                        $select .= ' LIMIT 0, 100';
                    }*/

                    $select = $wpdb->get_results($select);
                    //$this->recordCount = $wpdb->num_rows;
                    $iCount = 0;

                    foreach ($select as $val)
                    {
                        ?>
                        <tr>
                            <td><?php echo ++$iCount; ?></td>
                            <td><?php echo $val->post_id; ?></td>
                            <td><?php echo get_the_title($val->post_id); ?></td>
                            <td>
                                <?php 
                                    echo isset($val->hit_date) ? 
                                    date('d-m-Y', strtotime($val->hit_date))
                                    : 'All Time' 
                                ?>
                            </td>
                            <td><?php echo $val->hit_count; ?></td>
                            <td>
                                <a title="View Post" href="<?php echo get_permalink($val->post_id); ?>">
                                    <span class="dashicons dashicons-visibility"></span>
                                </a>
                            </td>
                        </tr>
                    <?php }  ?>
                </tbody>
            </table>
            <?php //$this->phcPagination(); ?>
        </div>
        <?php
    }

    
    /*protected function phcPagination()
    {
        if($this->recordCount == 100)
        {
            echo '<a href="'. admin_url('page=post_hit_counter' . '&pg='. ($page + 1), 'https') . '">NEXT >></a>';
        }
        
        if($this->recordCount == 100)
        {
            echo '<a href="'. admin_url('page=post_hit_counter' . '&pg='. ($page + 1), 'https') . '">NEXT >></a>';
        }
    }*/


    public function getFromUrl($key)
    {
        if (isset($_GET[$key]))
        {
            return $_GET[$key];
        }
        elseif (isset($_POST[$key]))
        {
            return $_POST[$key];
        }
    }


    /*
     * countPostRead
     * Function resposible for updating hit in database.
     */
    private function countPostRead()
    {
        if (!is_user_logged_in() && is_post_type())
        {
            global $wpdb;
            $postID = get_the_ID();
            $postHitCount = 1;
            $date = date('Y-m-d');

            $table = $wpdb->prefix . $this->table;

            $select = 'SELECT `id`, `hit_count` FROM ' . $table . ' WHERE DATE(hit_date)=CURDATE() AND `post_id`=' . $postID;

            $select = $wpdb->get_row($select);

            if (empty($select))
            {
                $wpdb->insert($table, array('post_id' => $postID, 'hit_count' => 1), array('%d', '%d'));
            }
            else
            {
                $id = $select->id;
                $hit_count = $select->hit_count + 1;
                $wpdb->query('UPDATE ' . $table . ' SET hit_count=' . $hit_count . ' WHERE id=' . $id);
            }
        }
    }
}
?>