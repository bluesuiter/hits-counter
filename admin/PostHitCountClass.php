<?php

class PostHitCount
{

    var $table = PHC_TABLE_NAME;
    var $keywordTable = 'search_keywords';
    var $keywordCountTable = 'search_keyword_count';
    var $recordCount = 0;
    var $recordLimit = 2;
    var $urlString = '';

    public function phcAdmin()
    {
        $this->postHitCounterAdmin();
    }


    public function phcCallCounter()
    {
        return wp_send_json($this->phcPostRead());
    }

    /**
     * method call to get totol hits on blog
     */
    public function phcTotalHitsCount($att = [])
    {
        global $wpdb;
        $table = $wpdb->base_prefix . $this->table;
        $sql = 'SELECT SUM(`hit_count`) as `hitCount` FROM ' . $table;

        if (phcReadArray($att, 'post_id'))
        {
            $sql .= ' WHERE post_id=' . $att['post_id'];
        }
        return $wpdb->get_var($sql);
    }

    /**
     * 
     */
    public function phcCounterAjax()
    {
        global $post;
        phcLoadTemplate('phc-counterajax', compact('post'));
    }

    /**
     * Add Menu for the Admin Panel
     */
    private function postHitCounterAdmin()
    {
        add_menu_page('Hits Counter', 'Hits Counter', 'manage_options', 'post_hit_counter', array($this, 'phcRecordAdmin'), 'dashicons-chart-bar', '55');
        add_submenu_page('post_hit_counter', 'Settings', 'Hits Counter Setting', 'manage_options', 'hit_counter_setting', array($this, 'phcSettingPanel'));
        add_submenu_page('post_hit_counter', 'Keyword Searches', 'Keyword Searches', 'manage_options', 'keyword_searches', array($this, 'phcKeywordSearches'));
        add_submenu_page('post_hit_counter', 'Hits Graph', 'Hits Graph', 'manage_options', 'hits_graph', array($this, 'hitsGraph'));
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

        phcLoadTemplate('phc-settings', compact('postTypes', 'setOption'));
    }


    /**
     * Post Hit Counts Showing Panel
     */
    public function phcRecordAdmin()
    {
        global $wpdb;
        wp_enqueue_script('jquery-ui-datepicker');

        $startDate = $endDate = current_time('Y-m-d');

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['find_record']))
        {
            $startDate = phcReadArray($_POST, 'start_date');
            $endDate = phcReadArray($_POST, 'end_date');
        }

        $table = $wpdb->prefix . $this->table;
        $select = 'SELECT `id`, `post_id`, `hit_count`, `hit_date`, `desktop`, `mobile` FROM ' . $table . ' WHERE DATE(hit_date) BETWEEN "' . $startDate . '" AND "' . $endDate . '"';

        if (phcReadArray($_GET, 'rcrdfnd') == 'all_time')
        {
            $select = 'SELECT `id`, `post_id`, SUM(`hit_count`) as `hit_count` FROM ' . $table . ' WHERE 1 GROUP BY `post_id` ';
        }
        $select .= 'ORDER BY `hit_count` DESC';
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
        $this->recordCount = $wpdb->num_rows;


        phcLoadTemplate('phc-records-panel', compact('startDate', 'endDate', 'select'));
?>

        <?php $this->phcPagination(); ?>
        </div>
<?php
    }


    protected function phcPagination()
    {
        $page = 0;
        if ($this->recordCount > $this->recordLimit)
        {
            echo '<a href="' . admin_url('page=post_hit_counter' . '&pg=' . ($page + 1), 'https') . '">NEXT >></a>';
        }

        // if($this->recordCount == $this->recordLimit)
        // {
        //     echo '<a href="'. admin_url('page=post_hit_counter' . '&pg='. ($page + 1), 'https') . '">NEXT >></a>';
        // }
    }

    public function phcKeywordSearches()
    {
        $result = $this->fectchKeyword('all_time');
        phcLoadTemplate('phcKeywordSearches', compact('result'));
    }

    /**
     * graphical representation of hit counts
     */
    public function hitsGraph()
    {
        global $wpdb;
        $select = "SELECT sum(hit_count) hit_count, DATE(hit_date) hit_date 
                    FROM " . $wpdb->prefix . $this->table . "  
                    GROUP BY DATE(hit_date)";

        $result = $wpdb->get_results($select);
        phcLoadTemplate('phc-graph', compact('result'));
    }

    /**
     * phcPostRead
     * Function responsible for updating hit in database.
     */
    private function phcPostRead()
    {
        try
        {
            global $wpdb;
            $postID = phcReadArray($_POST, 'post_id');
            $mobile = phcReadArray($_POST, 'mobile') == 'true' ? 1 : 0;
            $desktop = phcReadArray($_POST, 'desktop') == 'true' ? 1 : 0;
            $table = $wpdb->base_prefix . $this->table;

            if ($postID) :
                $select = "SELECT `id`, `hit_count` as `hitCount` FROM $table WHERE DATE(hit_date)=CURDATE() AND `post_id`=$postID";
                $select = $wpdb->get_row($select);

                if (empty($select))
                {
                    $result = $wpdb->insert($table, array('post_id' => $postID, 'hit_count' => 1, 'desktop' => $desktop, 'mobile' => $mobile), array('%d', '%d'));
                }
                else
                {
                    $result = $wpdb->query('UPDATE ' . $table . ' SET hit_count=hit_count+1, desktop=desktop+' . $desktop . ', mobile=mobile+' . $mobile . ' WHERE id=' . $select->id);
                }

                if (is_wp_error($result))
                {
                    return 'Error Hits Count:: ' . $result->get_error_message();
                }
                return 'success';
            endif;
        }
        catch (Exception $e)
        {
            return wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    private function fectchKeyword($timePeriod = 'today')
    {
        global $wpdb;
        $keyword = handlePostData('keyword');
        $table = $wpdb->base_prefix . $this->keywordTable;
        $tableOne = $wpdb->base_prefix . $this->keywordCountTable;

        if ($timePeriod === 'today')
        {
            $select = 'SELECT sk.id, sk.keyword, skc.hit_count FROM ' . $table . ' sk '
                . 'LEFT JOIN ' . $tableOne . ' skc ON skc.keyword_id = sk.`id` AND DATE(skc.hit_date)=CURDATE() '
                . 'WHERE sk.keyword=\'' . $keyword . '\' ';
            return $wpdb->get_row($select);
        }
        elseif ($timePeriod === 'all_time')
        {
            $select = 'SELECT sk.id, sk.keyword, SUM(skc.hit_count) as hitCount FROM ' . $table . ' sk '
                . 'LEFT JOIN ' . $tableOne . ' skc ON skc.keyword_id = sk.id GROUP BY skc.keyword_id ';

            $select .= !empty($keyword) ? 'WHERE sk.keyword=\'' . $keyword . '\' ' : '';
            return $wpdb->get_results($select);
        }
    }

    public function saveSearchQuery()
    {
        global $wpdb;
        $select = new \stdClass();
        $keyword = handlePostData('keyword');
        $table = $wpdb->base_prefix . $this->keywordTable;
        $tableOne = $wpdb->base_prefix . $this->keywordCountTable;

        if ($keyword) :
            $select = $this->fectchKeyword();
            if (empty($select->id))
            {
                $wpdb->insert($table, array('keyword' => $keyword), array('%s'));
                $select->id = $wpdb->insert_id;
            }

            if (empty($select->hit_count))
            {
                $data = array('keyword_id' => $select->id, 'hit_count' => ($select->hit_count + 1));
                $result = $wpdb->insert($tableOne, $data, array('%d', '%d'));
            }
            elseif (!empty($select->hit_count))
            {
                $result = $wpdb->query('UPDATE ' . $tableOne . ' SET hit_count=' . ($select->hit_count + 1) . ' WHERE '
                    . 'keyword_id=' . $select->id . ' AND DATE(hit_date)=CURDATE()');
            }

            if (is_wp_error($result))
            {
                return 'Error Hits Count:: ' . $result->get_error_message();
            }
            return 'hit_count';
        endif;
    }
}
?>