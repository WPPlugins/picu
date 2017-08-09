<script id="picu-approved" type="text/template">
	<div class="picu-modal-inner">
		<h1><?php _e( 'Thank you!', 'picu' ); ?></h1>
		<p><?php _e( 'The collection <strong><@= title @></strong> has been approved and the photographer has been notified.', 'picu' ); ?></p>
		<p><?php _e( 'You will be redirected in 5 seconds.', 'picu' ); ?></p>
		<?php
			$redirect = get_home_url();
			$redirect = apply_filters( 'picu_redirect', $redirect );
		?>
		<script>
			setTimeout( function(){ window.location = '<?php echo $redirect; ?>'; }, 5000 );
		</script>
	</div><!-- .picu-modal-inner -->
</script>