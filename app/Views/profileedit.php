<!doctype html>
<html>

<head>
    <title>Edit Profile - Twitter</title>
    <meta charset="UTF-8" />

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo site_url();?>/assets/images/bird.svg">

    	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css"/>
    <link rel='stylesheet' href='<?php echo site_url(); ?>assets/css/font-awesome.css' />
    <link rel='stylesheet' href='<?php echo site_url(); ?>assets/css/bootstrap.css' />
    <link rel="stylesheet" href="<?php echo site_url(); ?>assets/css/style-complete.css" />
    <link rel="stylesheet" href="<?php echo site_url(); ?>assets/css/style.css" />
    <script>
    // Inline JavaScript to define global URLs
    var baseUrl = "<?= base_url() ?>"; // Base URL for your application
    var siteUrl = "<?= site_url() ?>"; // Site URL for relative routes
    </script>
    <script src="<?php echo site_url(); ?>assets/js/jquery-3.1.1.min.js"></script>
    	<script src="https://code.jquery.com/jquery-3.1.1.js" integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA=" crossorigin="anonymous"></script>
</head>

<body>
    <div class="grid-container">

        <?php require 'left-sidebar.php' ?>


        <div class="main">

            <p class="page_title mb-0"><i class="fa fa-pencil-square-o mr-4" style="color:#50b7f5;"></i>Edit Profile</p>

            <div class='profile-box'>
                <div class='profile-cover mt-0'>
                    <!-- PROFILE-IMAGE -->
                    <img src="<?php echo site_url().$user->profileCover; ?>" />
                    <div class="img-upload-button-wrap">
                        <div class="img-upload-button1">
                            <label for="cover-upload-btn">
                                <i class="fa fa-camera" aria-hidden="true"></i>
                            </label>
                            <span class="span-text1">
                                Change your profile photo
                            </span>
                            <input id="cover-upload-btn" type="checkbox" />
                            <div class="img-upload-menu1">
                                <span class="img-upload-arrow"></span>
                                <?= form_open_multipart('profileedit/profilecoverimage') ?>
                                    <ul>
                                        <li>
                                            <label for="file-up">
                                                Upload photo
                                            </label>
                                            <input type="file" onchange="this.form.submit();" name="profileCover" id="file-up" />
                                        </li>
                                        <li>
                                            <label for="cover-upload-btn">
                                                Cancel
                                            </label>
                                        </li>
                                    </ul>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='profile-body'>
                    <div class="profile-header">
                        <div class="profile-image">
                            <img src="<?php echo site_url().$user->profileImage; ?>" />
                            <div class="img-upload-button-wrap1">
                                <div class="img-upload-button">
                                    <label for="img-upload-btn">
                                        <i class="fa fa-camera" aria-hidden="true"></i>
                                    </label>
                                    <!--
                                    <span class="span-text">
                                        Change your profile photo
                                    </span>
-->
                                    <input id="img-upload-btn" type="checkbox" />
                                    <div class="img-upload-menu">
                                        <span class="img-upload-arrow"></span>
                                        <?= form_open_multipart('profileedit/profileimage') ?>
                                            <ul>
                                                <li>
                                                    <label for="profileImage">
                                                        Upload photo
                                                    </label>
                                                    <input id="profileImage" type="file" onchange="this.form.submit();" name="profileImage" />

                                                </li>
                                                <li><a href="#">Remove</a></li>
                                                <li>
                                                    <label for="img-upload-btn">
                                                        Cancel
                                                    </label>
                                                </li>
                                            </ul>
                                        <?= form_close() ?>
                                    </div>
                                </div>
                                <!-- img upload end-->
                            </div>
                        </div>
                        <div class="edit-button d-flex">
                            <span>
                                <button class="new-btn mr-3" type="button" onclick="window.location.href='<?php echo site_url().$user->username;?>'" value="Cancel" style="outline:none;">Cancel</button>
                            </span>
                            <span>
                                <button class="new-btn" type="submit" id="save" value="Save Changes" style="outline:none;">Save</button>
                            </span>

                        </div>
                    </div>
                    <div class="profile-text">

                        <form id="editForm" action="<?= site_url('profileedit/submit'); ?>" method="post" enctype="multipart/Form-data" autocomplete="off">
                            <?php if(isset($imgError)){echo '<li>'.$imgError.'</li>';}?>
                            <div class="profile-name-wrap">
                                <div class="form-group">
                                    <label class="ml-1">Name</label>
                                    <input type="text" class="form-control" name="screenName" value="<?php echo $user->screenName;?>" />
                                </div>
                                <!--
                                <div class="profile-tname">
                                    @<?php echo $user->username;?>
                                </div>
-->
                            </div>

                            <div class="form-group">
                               <label class="ml-1">Location</label>
                                <input class="form-control" id="cn" type="text" name="country" placeholder="Country" value="<?php echo $user->country;?>" />
                            </div>


                            <div class="form-group">
                               <label class="ml-1">Website</label>
                                <input class="form-control" type="text" name="website" placeholder="Website" value="<?php echo $user->website;?>" />
                            </div>

                            <div class="profile-bio-wrap">
                                <div class="form-group">
                                    <label class="ml-1">Bio</label>
                                    <textarea class="status form-control" name="bio"><?php echo $user->bio;?></textarea>
                                    <div class="hash-box">
                                        <ul>
                                        </ul>
                                    </div>
                                </div>
                            </div>



                            <?php if(isset($error)){echo '<li>'.$error.'</li>';}?>
                        </form>
                        <script type="text/javascript">
                            $('#save').click(function() {
                                $('#editForm').submit();
                            });

                        </script>


                    </div>
                </div>
            </div>

        </div>


        <div class="popupTweet"></div>

        <script type='text/javascript' src='<?php echo site_url();?>assets/js/search.js'></script>
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/hashtag.js'></script>

        <?php require 'right-sidebar.php' ?>

        <script type='text/javascript' src='<?php echo site_url();?>assets/js/follow.js'></script>

        <script src='<?php echo site_url();?>assets/js/jquery-3.1.1.min.js'></script>
        <script src='<?php echo site_url();?>assets/js/popper.min.js'></script>
        <script src='<?php echo site_url();?>assets/js/bootstrap.min.js'></script>

        <!-- SCRIPTS -->
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/comment.js'></script>
        <script type='text/javascript' src='<?php echo site_url();?>assets/js/fetch.js'></script>
        <script type="text/javascript" src="<?php echo site_url();?>assets/js/popuptweets.js"></script>
        <script type="text/javascript" src="<?php echo site_url();?>assets/js/delete.js"></script>
        <script type="text/javascript" src="<?php echo site_url();?>assets/js/popupForm.js"></script>
        <script type="text/javascript" src="<?php echo site_url();?>assets/js/retweet.js"></script>
        <script type="text/javascript" src="<?php echo site_url();?>assets/js/like.js"></script>
        <script type="text/javascript" src="<?php echo site_url();?>assets/js/hashtag.js"></script>
        <script type="text/javascript" src="<?php echo site_url();?>assets/js/search.js"></script>
        <script type="text/javascript" src="<?php echo site_url();?>assets/js/follow.js"></script>
        <script type="text/javascript" src="<?php echo site_url();?>assets/js/messages.js"></script>
        <script type="text/javascript" src="<?php echo site_url();?>assets/js/notification.js"></script>
        <script type="text/javascript" src="<?php echo site_url();?>assets/js/postMessage.js"></script>


</body>

</html>
