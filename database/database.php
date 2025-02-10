<?php

class phcDataBaseClass
{

    private $tableName = 'hitsCount';

    /**
     * This Will Add Table for plugin
     */
    public function phcInstallDataTables()
    {
        $this->phcAddDataTable();
    }

    /**
     * This Will Check and Alter Table for plugin
     */
    public function phcUpdatesCheck()
    {
        $this->phcAlterTable();
    }

    /**
     * This Will Add Table for plugin
     */
    private function phcAddDataTable()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->base_prefix . $this->tableName;

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE `$table_name` (
                     `id` int(11) PRIMARY KEY AUTO_INCREMENT,
                     `post_id` bigint(20) NOT NULL,
                     `hit_count` int(11) NOT NULL DEFAULT '0',
                     `hit_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                     `desktop` INT NOT NULL DEFAULT '0',
                     `mobile` INT NOT NULL DEFAULT '0'
                  ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        $table_name = $wpdb->base_prefix . 'search_keyword_count';
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE `$table_name` (
                        `id` int(11) NOT NULL,
                        `keyword_id` int(11) DEFAULT NULL,
                        `hit_count` int(11) NOT NULL DEFAULT '0',
                        `hit_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                        ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    /**
     * This Will Alter Table for plugin
     */
    private function phcAlterTable()
    {
        global $wpdb;
        $table_name = $wpdb->base_prefix . $this->tableName;

        $version = get_option('phc_version_');
        if (!$version || $version < '2.0.23') {
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
                $sql = "ALTER TABLE `$table_name` 
                    ADD COLUMN `desktop` INT NOT NULL DEFAULT '0' AFTER hit_date,
                    ADD COLUMN `mobile` INT NOT NULL DEFAULT '0' AFTER desktop";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);

                add_option('phc_version_', '2.0.23');
            }
        }
    }
}
