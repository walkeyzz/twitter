<html>

<head>
    <title>Accounts Settings - Twitter</title>
    <meta charset='UTF-8' />

    <link rel='shortcut icon' type='image/x-icon' href='<?php echo site_url(); ?>assets/images/bird.svg'>

    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css' />
    <script>
    // Inline JavaScript to define global URLs
    var baseUrl = "<?= base_url() ?>"; // Base URL for your application
    var siteUrl = "<?= site_url() ?>"; // Site URL for relative routes
    </script>
    <script src='https://code.jquery.com/jquery-1.10.2.js'></script>

    <link rel='stylesheet' href='<?php echo site_url(); ?>assets/css/font-awesome.css' />
    <link rel='stylesheet' href='<?php echo site_url(); ?>assets/css/bootstrap.css' />
    <link rel='stylesheet' href='<?php echo site_url(); ?>assets/css/style-complete.css' />
    <link rel='stylesheet' href='<?php echo site_url(); ?>assets/css/style.css' />
    <script src='<?php echo site_url(); ?>assets/js/jquery-3.1.1.min.js'></script>
</head>

<body>
    <div class='grid-container'>

        <?php require 'left-sidebar.php' ?>

        <div class='main'>
            <p class='page_title mb-0' style='border-bottom:none;'><i class='fa fa-cog mr-4' style='color:#50b7f5;'></i>Settings</p>

            <div class='setting-head'>
                <div class='account-text active'>
                    <a class='bold' href='<?php echo site_url()?>settings/account'>Account</a>
                </div>
                <div class='password-text'>
                    <a href='<?php echo site_url();?>settings/password'>Password</a>
                </div>
            </div>

            <div class='righter mt-4'>
                <div class='inner-righter'>
                    <div class='acc'>
                        <div class='acc-heading'>
                            <h5>Change your basic account settings</h5>
                        </div>
                        <div class='acc-content'>
                            <?= form_open('settings/account/submit'); ?>
                                <div class='acc-wrap'>
                                    <label class='ml-3' for=''>Username</label>
                                    <div class='form-group col-auto'>
                                        <input class='form-control' type='text' name='username' value="<?php echo $user->username;?>" />
                                        <span>
                                            <?php if ( isset( $error['username'] ) ) {
    echo $error['username'];
}
?>
                                        </span>
                                    </div>
                                </div>

<!--
                                <div class='acc-wrap'>
                                    <label class='ml-3' for=''>Email</label>
                                    <div class='form-group col-auto'>
                                        <input class='form-control' type='text' name='email' value="" />
                                        <span>
                                        </span>
                                    </div>
                                </div>
-->
                                <div class='acc-wrap'>
                                    <div class='acc-right mt-3'>
                                        <button class='new-btn' type='Submit' id='save' name='submit' value='Save changes'>Save</button>
                                    </div>
                                    <div class='settings-error'>
                                        <?php if ( isset( $error['fields'] ) ) {
                                            echo $error['fields'];
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?= form_close() ?>
                        </div>
                    </div>
                    <div class='content-setting'>
                        <div class='content-heading'>

                        </div>
                        <div class='content-content'>
                            <div class='content-left'>

                            </div>
                            <div class='content-right'>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--RIGHTER ENDS-->
        </div>
        <!--CONTAINER_WRAP ENDS-->

        <div class='popupTweet'></div>

        <script type='text/javascript' src='<?php echo site_url();?>assets/js/search.js'></script>
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/hashtag.js'></script>

        <?php require 'right-sidebar.php' ?>

        <script type='text/javascript' src='<?php echo site_url();?>assets/js/follow.js'></script>

        <script src='<?php echo site_url();?>assets/js/jquery-3.1.1.min.js'></script>
        <script src='<?php echo site_url();?>assets/js/popper.min.js'></script>
        <script src='<?php echo site_url();?>assets/js/bootstrap.min.js'></script>

        <!-- SCRIPTS -->
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/like.js'></script>
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/retweet.js'></script>
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/popuptweets.js'></script>
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/delete.js'></script>
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/comment.js'></script>
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/popupForm.js'></script>
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/fetch.js'></script>
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/messages.js'></script>
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/notification.js'></script>
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/postMessage.js'></script>

</body>

</html>
