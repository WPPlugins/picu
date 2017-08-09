<script id="picu-send-selection" type="text/template">
    <div class="picu-modal-inner">
        <h1><?php _e( 'Approve Collection', 'picu' ); ?>: <@= title @></h1>
        <div class="info-panel">
            <div class="panel-item">
                <div class="panel-value"><@= imagecount @></div>
                <div class="panel-label"><?php _e( 'Images', 'picu'); ?></div>
            </div>
            <div class="panel-item">
                <div class="panel-value"><@= selected @></div>
                <div class="panel-label"><?php _e( 'selected', 'picu'); ?></div>
            </div>
        </div>
        <p><?php _e('You are about to approve <strong><span class="picu-selected-num"><@= selected @></span> image(s)</strong> of <strong><@= title @></strong>.', 'picu' ); ?></p>
        <p><label for="picu-approval-message"><?php _e( 'Anything else you want us to know?', 'picu' ); ?></label> <textarea id="picu-approval-message" name="picu_approval_message" placeholder="<?php _ex( 'Leave a comment…', 'approval message placeholder', 'picu' ); ?>"></textarea></p>
        <a id="picu-send-button" class="picu-button primary" href="#send"><?php _ex( 'approve selection', 'send selection button text', 'picu' ); ?></a>
        <a class="picu-close-modal" href="#index"><svg viewBox="0 0 100 100"><use xlink:href="#icon_close"></use></svg><span><?php _e( 'close', 'picu' ); ?></span></a>
    </div><!-- .picu-modal-inner -->
</script>