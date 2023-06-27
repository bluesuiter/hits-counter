<?php

class phcDataBaseClass{

    var $tableName = 'hitsCount';

    public function phcInstallDataTables()
    {
        $this->phcAddDataTable();
    }

    /**
     * This Will Add Table for plugin
     */
    private function phcAddDataTable()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . $this->tableName;
        
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE `$table_name` (
                     `id` int(11) PRIMARY KEY AUTO_INCREMENT,
                     `post_id` bigint(20) NOT NULL,
                     `hit_count` int(11) NOT NULL DEFAULT '0',
                     `hit_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                  ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        // added device type
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            $sql = "ALTER TABLE `$table_name` 
                        ADD COLUMN `desktop` INT NOT NULL DEFAULT '0' AFTER `hit_count`,
                        ADD COLUMN `mobile` INT NOT NULL DEFAULT '0' AFTER `desktop`;
                  ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
}
?>
