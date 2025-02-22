<div class="post_hit_record">
    <h1 class="wp-heading-inline">Post Hit Counts Record</h1>
    <div class="alignleft">
        <form method="post" action="<?php echo admin_url('?page=post_hit_counter') ?>">
            <input type="text" name="start_date" class="input datepicker" placeholder="Start Date" readonly value="<?= $startDate ?>" />
            <input type="text" name="end_date" class="input datepicker" placeholder="End Date" readonly value="<?= $endDate ?>" />
            <input type="submit" class="button button-primary" name="find_record" value="Find Record" />
        </form>
    </div>
    <div class="alignright">
        <a class="button" href="<?= admin_url('?page=post_hit_counter&rcrdfnd=all_time') ?>">
            All Time Hit Count
        </a>
    </div>
    <div style='margin: 1% 0;display:inline-block;'>
        <table class="widefat fixed striped" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Post ID</th>
                    <th>Post Name</th>
                    <th>Date</th>
                    <th>Desktop/Mobile</th>
                    <th>Total Count</th>
                    <th>View Post</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($select as $key => $val)
                {
                ?>
                    <tr>
                        <td><?= $val->post_id; ?></td>
                        <td><?= get_the_title($val->post_id); ?></td>
                        <td>
                            <?= isset($val->hit_date) ? date('d-m-Y', strtotime($val->hit_date)) : 'All Time' ?>
                        </td>
                        <td><?= $val->desktop ?? ''; ?>/<?= $val->mobile ?? ''; ?></td>
                        <td><?= $val->hit_count; ?></td>
                        <td>
                            <a title="View Post" href="<?= get_permalink($val->post_id); ?>">
                                <span class="dashicons dashicons-visibility"></span>
                            </a>
                        </td>
                    </tr>
                <?php }  ?>
            </tbody>
        </table>
    </div>
</div>

<style type="text/css">
    .post_hit_record {
        display: block;
        max-width: 98%;
		height: 90vh;
    }

    .post_hit_record table {
        text-align: center;
        display: table;
    }

    .post_hit_record table th {
        font-weight: bold;
        text-align: center;
        padding: 8px 10px;
    }

    .post_hit_record table td {
        padding: 8px 10px;
    }

    .post_hit_record input[type="text"] {
        background: #fff;
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

    .ui-state-active {
        background: #0073aa;
        color: #fff;
    }

    .ui-datepicker-title select.ui-datepicker-year,
    .ui-datepicker-title select.ui-datepicker-month {
        display: inline-block;
        height: 24px;
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
    jQuery(document).ready(function() {
        jQuery('.datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            inline: false,
            dateFormat: "yy-mm-dd"
        });
    });
</script>