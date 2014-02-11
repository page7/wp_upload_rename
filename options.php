
<div class="wrap">
    <h2><?php echo $this->__('WP Upload Rename Settings'); ?></h2>

    <style type="text/css">
    .wp_upload_rename_option{ padding-left:30px; }
    .wp_upload_rename_option b{ text-decoration:underline; }
    </style>

    <div id="wp_upload_rename_options" class="inside">
        <form method="post" action="options.php">
            <?php
                wp_nonce_field('update-options');
                settings_fields('wp_upload_rename_setting');
            ?>

            <h3><?php echo $this->__('Setting'); ?></h3>
            <div class="wp_upload_rename_option">
                <h4><?php echo $this->__('Rename to'); ?>:</h4>
                <select name="wp_upload_rename_options[mode]" onchange="wp_upload_rename_selectMode(this)">
                    <option value="char" <?php echo $this->option('mode') == 'char' ? 'selected' : ''; ?>><?php echo $this->__('Random Chars'); ?></option>
                    <option value="num" <?php echo $this->option('mode') == 'num' ? 'selected' : ''; ?>><?php echo $this->__('Random Numbers'); ?></option>
                    <option value="date" <?php echo $this->option('mode') == 'date' ? 'selected' : ''; ?>><?php echo $this->__('Date & Time'); ?></option>
                    <option value="diy" <?php echo $this->option('mode') == 'diy' ? 'selected' : ''; ?>><?php echo $this->__('DIY'); ?></option>
                </select>
            </div>

            <div class="wp_upload_rename_option">
                <h4><?php echo $this->__('Length'); ?>:</h4>
                <p><?php echo $this->__('Set length of the random chars / numbers.'); ?></p>
                <input id="wp_upload_rename_length" style="width:60px;" name="wp_upload_rename_options[length]" value="<?php echo $this->option('length'); ?>" />
            </div>

            <div class="wp_upload_rename_option">
                <h4><?php echo $this->__('Param'); ?>:</h4>
                <p><?php echo $this->__('Set value of some chars in char mode, eq:"ABCDEFGHIabcdefg_123"<br /> or a date format string in time mode, eq:"Y_m_d_H_i_s"<br /> or use <b>%file%</b> , <b>%date|Y-m-d%</b> , <b>%chars|5%</b> , <b>%nums|7%</b> in diy mode.'); ?></p>
                <input id="wp_upload_rename_param" style="width:400px;" name="wp_upload_rename_options[param]" value="<?php echo $this->option('param'); ?>" />
            </div>

            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="wp_upload_rename_setting" value="wp_upload_rename_options" />
            <div style="padding:20px 0px 30px 30px ;">
                <input type="submit" class="button-primary" value="<?php echo $this->__('Save'); ?>" />
            </div>
        </form>

        <hr />

        <h3><?php echo $this->__('Support'); ?></h3>
        <div class="wp_upload_rename_option">
            If you have any <b>Questions</b> or good <b>Ideas</b>. Please <a href="mailto:zhounan0120@gmail.com">Mail to me</a>,<br />
            or go to <a href="https://github.com/page7/wp_upload_rename/issues" target="_blank">Github Issue</a>.<br />
            I will add media's types option and a popup before upload in future.<br />
            If you like it, <a href="http://wordpress.org/plugins/easy-table/" target="_blank">Rate It Here</a>, and I won't mind if you want to buy me a cup of coffee. :)
            <form id="donate" name="_xclick" action="https://www.paypal.com/us/cgi-bin/webscr" method="post" target="_blank">
            $ <input style="border:#CCC solid 1px;" type="text" name="amount" value="2.00" /><br />
            <input type="hidden" name="cmd" value="_xclick" />
            <input type="hidden" name="business" value="zhounan0120@gmail.com" />
            <input type="hidden" name="item_name" value="Donate for wp_upload_rename" />
            <input type="hidden" name="currency_code" value="USD" />
            <input type="image" src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_cc_147x47.png" border="0" name="submit" alt="Donate" />
            </form>
            <?php echo $this->__('Thanks to '); ?>
            <ul>
                <li><a target="_blank" href="<?php echo site_url();?>">You</a></li>
                <li><a target="_blank" href="http://php.net">PHP</a></li>
                <li><a target="_blank" href="http://wordpress.org">WordPress</a></li>
            </ul>
        </div>
    </div>
</div>

<script type="text/javascript">
wp_upload_rename_selectMode(dom){
    var j = jQuery(dom);
    jQuery("#wp_upload_rename_length, #wp_upload_rename_options").removeAttr("disabled");
    if(j.val() == 'date' || j.val() == 'diy'){
        jQuery("#wp_upload_rename_length").attr("disabled");
    }else if(j.val() == 'num'){
        jQuery("#wp_upload_rename_options").attr("disabled");
    }
}
</script>