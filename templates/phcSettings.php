<h1 class="wp-heading-inline">Setting Hits Counter</h1>
<div class="">
    <form name="" action="" method="post">
        <label><strong>Capture Post Hits For:</strong></label>
        <?php
        foreach ($postTypes as $postType)
        {
            $checked = isset($setOption[$postType->name]) && $setOption[$postType->name] == 1 ? 'checked' : '';
            echo '<p><input type="checkbox" name="' . $postType->name . '" ' . $checked . ' value="1"/>';
            echo '<label>' . $postType->label . '</label></p>';
        }
        ?>
        <button type="submit" name="save_hcs" class="button button-primary">Submit</button>
    </form>
</div>