<h1 class="wp-heading-inline">Keyword searches on Website</h1>
<div>
    <table class="wp-list-table widefat fixed striped">    
        <thead>
            <tr>
                <th>Sr No.</th>
                <th>KeyWord</th>
                <th>Hit Count</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($result as $res){ ?>
                <tr>
                    <td><?php echo $res->id ?></td>
                    <td><?php echo $res->keyword ?></td>
                    <td><?php echo $res->hitCount ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>