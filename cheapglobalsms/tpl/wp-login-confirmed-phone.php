<?php if (!defined('ABSPATH')) die('Cannot be accessed directly!'); ?>

<p class="message">
    <?php echo __('Success! Your mobile is now connected to your profile, which helps secure the integrity of your account and this website.', 'cheapglobalsms'); ?>
    <br/><br/>
    <strong><?php echo __('You have also been logged in 😄', 'cheapglobalsms'); ?></strong>
    <br><br>

    <span class="submit">
        <a href="<?= $redirect_to ?>" class="button button-primary button-large"><?php _e('Continue', 'cheapglobalsms'); ?></a>
    </span>
    <br>
    <br>
</p>

