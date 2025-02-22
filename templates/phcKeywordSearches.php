<div id="wpwrap">
    <h1 class="wp-heading-inline">Keyword searches on Website</h1>
    <div>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>KeyWord</th>
                    <th>Hit Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $res)
                { ?>
                    <tr>
                        <td><?= $res->id ?></td>
                        <td><?= $res->keyword ?></td>
                        <td><?= $res->hitCount ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>