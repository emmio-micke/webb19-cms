<form method="post">
	<!-- some inputs here ... -->
	<?php wp_nonce_field( 'name_of_my_action', 'name_of_nonce_field' ); ?>
</form>
