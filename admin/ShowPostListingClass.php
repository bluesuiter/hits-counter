<?php

class ShowPostListing extends phcDataBaseClass {

    var $attr = [];

    public function phcPostListActions() {
        add_shortcode('getMostRead', [$this, 'getMostReadPosts']);

        /* Ajax call */
        add_action('wp_ajax_nopriv_phcPost_listing', [$this, 'ajaxCallHandler']);
    }

    /**
     * getMostReadPosts
     * @global object $wpdb
     * @param array $atts
     * @return json, array
     */
    public function getMostReadPosts($atts = array()) {
        global $wpdb;
        $table = $wpdb->prefix . $this->tableName;

        $period = '';
        if (isset($atts['period'])) {
            if ($atts['period'] === 'month') {
                $period = $this->timePeriod($atts);
            } elseif ($atts['period'] === 'days') {
                $period = $this->timePeriod($atts);
            } elseif ($atts['period'] === 'today') {
                $period = $this->timePeriod($atts);
            } elseif ($atts['period'] === 'all'){
                $period = '';
            }
        }

        $sqlQry = "SELECT post_id as ID "
                . "FROM $table "
                . "WHERE 1 $period "
                . "GROUP BY post_id "
                . "ORDER BY SUM(hit_count) DESC";

        if (isset($atts['count'])) {
            $sqlQry .= " LIMIT " . $atts['count'];
        }

        $result = $wpdb->get_results($sqlQry, ARRAY_A);

        if (is_wp_error($result)) {
            $error_string = $result->get_error_message();
            echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
            return false;
        }
        return array_column($result, 'ID');
    }

    /**
     *  Ajax Call Handler */
    public function ajaxCallHandler() {
        if (!empty($_GET)) {
            foreach ($_GET as $k => $v) {
                $atts[$k] = htmlspecialchars(trim($v));
            }
        }

        $format = isset($atts['format']) ? $atts['format'] : 'array';

        $result = [];
        $siteUrl = site_url("/");
        $dateFormat = get_site_option('date_format');
        $postList = $this->getMostReadPosts($atts);
        $size = 'full';
        if (!empty($postList)) {
            foreach ($postList as $post) {
                $postId = $post['ID'];
                $author = $post['post_author'];

                $post = get_post($postId);
                $result[] = ['post_id' => $postId,
                    'post_title' => $post['post_title'],
                    'post_permalink' => $siteUrl . $post['post_name'],
                    'post_thumbnail' => get_the_post_thumbnail_url($postId, $size),
                    'post_date' => get_the_date($dateFormat, $postId),
                    'post_author' => get_the_author_meta('user_firstname', $author) . ' ' . get_the_author_meta('user_lastname', $author),];
            }
            return ($format == 'json' ? wp_send_json($result) : $result);
        }
        wp_die();
    }

    private function timePeriod($atts) {
        if (!isset($atts['period'])) {
            return false;
        }

        switch ($atts['period']) {
            case 'month':
                $year = (isset($atts['year']) ? $atts['year'] : date('Y'));
                $month = (isset($atts['month']) ? $atts['month'] : date('m'));
                $query = " AND YEAR(hit_date)=" . $year . " AND MONTH(hit_date)=" . $month . " ";
                break;
            case 'days':
                $days = (isset($atts['days']) ? $atts['days'] : 7);
                $query = 'AND hit_date BETWEEN CURDATE() - INTERVAL ' . $days . ' DAY AND CURDATE() ';
                break;
            case 'today':
                $days = (isset($atts['days']) ? $atts['days'] : 7);
                $query = 'AND hit_date=CURDATE() ';
                break;
            default:
                break;
        }
        return $query;
    }

}
