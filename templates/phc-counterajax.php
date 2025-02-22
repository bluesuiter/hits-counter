<script type="text/javascript">
    function getQueryStringValue(key) {
        return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
    }

    <?php
    if (!is_user_logged_in())
    {
        if (is_singular() || phcPostType())
        { ?>

            function countHit() {
                let desktop, mobile = false;

                if ((navigator.userAgent).match('Mobile')) {
                    mobile = true;
                } else {
                    desktop = true;
                }

                const attr = {
                    'action': 'post_read',
                    'post_id': <?= $post->ID ?>,
                    'post_type': '<?= $post->post_type ?>',
                    'mobile': mobile,
                    'desktop': desktop
                };
                jQuery.post("<?= admin_url("admin-ajax.php") ?>", attr, function(res) {});
            }
            countHit();
        <?php
        }
        elseif (is_search())
        {
        ?>

            function captureSearchQuery() {
                var attr = {
                    "action": "rcrd_srch_query",
                    "mobile": window.mobilecheck,
                    "keyword": "<?= get_query_var('s') ?>"
                };
                jQuery.post("<?= admin_url("admin-ajax.php") ?>", attr, function(res) {});
            }
            captureSearchQuery();
    <?php }
    }
    ?>
</script>