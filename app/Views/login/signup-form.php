
<?= form_open('signup'); ?>
	<?php if (!empty($error)) : ?>
			<div class="alert alert-danger" role="alert" style="width: 300px; margin:20px auto;text-align:center;">
					<?php if(is_array($error)){
						echo implode('<br>', $error);
					}else{
						echo $error;
					}?>
			</div>
	<?php endif; ?>
	<div class="signup-form">
					<div class="form-group">
						<input class="form-control" type="text" name="screenName" placeholder="Full Name" />
					</div>
					<div class="form-group">
						<input class="form-control" type="email" name="email" placeholder="Email" />
					</div>
					<div class="form-group">
						<input class="form-control" type="password" name="password" placeholder="Password" />
					</div>
					<input class="new-btn m-auto mt-5" type="submit" name="signup" Value="Signup">
	</div>

<?= form_close(); ?>

<script type="text/javascript">
        setTimeout(function() {
            // Closing the alert
            $('#alert').alert('close');
        }, 3500);

    </script>
