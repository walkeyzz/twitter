<?= form_open('signin', ['autocomplete' => 'off']); ?> <!-- POST form to authenticate -->

<h1 class="mb-4" style="text-align:center;">Log in to Twitter</h1>

<div class="form-group form-row">
    <input class="form-control col-4 mr-3 ml-5 mt-1 p-3" name="email" type="text" placeholder="Email" style="height:50px;" />
    <input class="form-control col-4 mr-3 mt-1 p-3" name="password" type="password" placeholder="Password" style="height:50px;" />

    <input class="new-btn col-2 mt-1" name="login" type="submit" value="login" style="height: 50px; font-size:20px;">
</div>

<?php if (isset($errorMsg)): ?>
    <div class="alert alert-danger" role="alert" style="width: 400px; margin:20px auto;text-align:center;">
        <?= esc($errorMsg); ?> <!-- Display error message -->
    </div>
<?php endif; ?>

<?= form_close(); ?> <!-- Close form -->
