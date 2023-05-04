<?php

class PostHitCount
{

    var $table = 'hitsCount';
    var $recordCount = 0;
    var $recordLimit = 100;
    var $urlString = '';

    public function phcAdmin()
    {
        $this->postHitCounterAdmin();
    }

    public function phcCallCounter()
    {
        $this->phcPostRead();
    }

    /**
     * Add Menu for the Admin Panel
     */
    private function postHitCounterAdmin()
    {
        add_menu_page('Hits Counter', 'Hits Counter', 'manage_options', 'post_hit_counter', array($this, 'phcRecordAdmin'), 'dashicons-chart-bar', '55');
        add_submenu_page('post_hit_counter', 'Settings', 'Hits Counter Setting', 'manage_options', 'hit_counter_setting', array($this, 'phcSettingPanel'));
    }

    /**
     * Enable Post Hit Counter for Specific Post Types
     */
    public function phcSettingPanel()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_hcs']))
        {
            unset($_POST['save_hcs']);
            update_option('post_type_hits_cout_', serialize($_POST));
        }

        $setOption = unserialize(get_option('post_type_hits_cout_'));

        $args = array('public' => true, 'show_ui' => true, 'exclude_from_search' => false, 'show_in_nav_menus' => true);
        $postTypes = get_post_types($args, 'objects', 'and');

        return include(__DIR__ . '/../templates/phcSettings.php');
    }

    /**
     * Post Hit Counts Showing Panel
     */
    public function phcRecordAdmin()
    {
        global $wpdb;
        wp_enqueue_script('jquery-ui-datepicker');

        $startDate = $endDate = date('Y-m-d');
        $period = 'All Time';

        $table = $wpdb->prefix . $this->table;
        $select = 'SELECT `id`, `post_id`, `hit_count`, `hit_date` FROM ' . $table . ' WHERE DATE(hit_date) BETWEEN "' . $startDate . '" AND "' . $endDate . '"';

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['find_record']))
        {
            $startDate = phcReadArray($_POST, 'start_date');
            $endDate = phcReadArray($_POST, 'end_date');

            $select = 'SELECT `id`, `post_id`, SUM(`hit_count`) as `hit_count` FROM ' . $table . ' WHERE ';
            $select .= 'DATE(hit_date) BETWEEN "' . $startDate . '" AND "' . $endDate . '"';
            $period = $startDate . ' - ' . $endDate;
        }

        if (phcReadArray($_GET, 'rcrdfnd') == 'all_time')
        {
            $select = 'SELECT `id`, `post_id`, SUM(`hit_count`) as `hit_count` FROM ' . $table . ' WHERE 1 ';
        }

        $select .= ' GROUP BY `post_id` ORDER BY `hit_count` DESC';

        /* if(isset($_GET['pg']) && $_GET['pg']!='')
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
                          } */

        $select = $wpdb->get_results($select);

        return include(__DIR__ . '/../templates/phcRecords.php');
    }

    /* protected function phcPagination()
          {
          if($this->recordCount == 100)
          {
          echo '<a href="'. admin_url('page=post_hit_counter' . '&pg='. ($page + 1), 'https') . '">NEXT >></a>';
          }

          if($this->recordCount == 100)
          {
          echo '<a href="'. admin_url('page=post_hit_counter' . '&pg='. ($page + 1), 'https') . '">NEXT >></a>';
          }
          } */


    /*
         * countPostRead
         * Function resposible for updating hit in database.
         */

    private function phcPostRead()
    {
        if (!is_user_logged_in() && phcPostType())
        {
            global $wpdb;
            $postID = get_the_ID();
            $table = $wpdb->prefix . $this->table;

            $select = 'SELECT `id`, `hit_count` as `hitCount` FROM ' . $table . ' WHERE DATE(hit_date)=CURDATE() AND `post_id`=' . $postID;
            $select = $wpdb->get_row($select);

            if (empty($select))
            {
                $result = $wpdb->insert($table, array('post_id' => $postID, 'hit_count' => 1), array('%d', '%d'));
            }
            else
            {
                $recordId = $select->id;
                $hitCount = $select->hitCount + 1;
                $result = $wpdb->query('UPDATE ' . $table . ' SET hit_count=' . $hitCount . ' WHERE id=' . $recordId);
            }

            if (is_wp_error($result))
            {
                echo 'Error Hits Count:: ' . $result->get_error_message();
            }
        }
    }
}
