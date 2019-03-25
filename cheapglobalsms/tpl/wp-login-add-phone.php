<?php if (!defined('ABSPATH')) die('Cannot be accessed directly!'); ?>
<?php login_header(__('Register mobile phone number', 'cheapglobalsms')); ?>

    <div class="step current">

        <p class="message">
            <?php _e('Please add your cellphone number below, so we can send you a one-time verification code.<br />This is mandatory, but free of charge to you.', 'cheapglobalsms'); ?>
            <br/>
        </p>

        <form method="post" id="cgsms_add_phone_form">

            <input type="hidden" name="cgsms_2f_tmp" value="<?= esc_attr(CgsmsSecurityTwoFactor::$TMP_TOKEN); ?>">

            <p>
                <label for="mcc"><?php _e('Mobile country code', 'cheapglobalsms'); ?></label><br>
                <select name="mcc" id="cgsms_mcc" data-cgsms-mobile-cc data-default-cc="45" style="width: 100%"
                        class="input"></select>
            </p>

            <p>
                <label for="cgsms_mno"><?php _e('Mobile number', 'cheapglobalsms'); ?><br>
                    <input type="number" name="mno" id="cgsms_mno" class="input" value="" size="20">
                </label>
            </p>

            <p class="submit">
                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large"
                       value="<?php esc_attr_e('Verify phone', 'cheapglobalsms'); ?>" data-loading="<?= esc_attr_e('Sending SMS...', 'cheapglobalsms'); ?>">
            </p>
        </form>
    </div>


<?php login_footer(); ?>