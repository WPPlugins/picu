<script id="picu-info-view" type="text/template">
	<div class="picu-modal-inner">
		<h1><@= title @></h1>
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
		<div class="description"><@= description @></div>
		<a class="picu-button primary picu-start-selection" href="#index">
		<@ if ( picu.poststatus != 'approved' ) { @>
		<?php _e( 'OK', 'picu' ); ?>
		<@ } else { @>
		<?php _e( 'View collection', 'picu' ); ?>
		<@ } @>
		</a>
		<a class="picu-close-modal" href="#index"><svg viewBox="0 0 100 100"><use xlink:href="#icon_close"></use></svg><span><?php _e( 'close', 'picu' ); ?></span></a>
	</div><!-- .picu-modal-inner -->
</script>