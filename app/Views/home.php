<!DOCTYPE HTML>
<html>

<head>
    <title>Home - Tweety</title>
    <meta charset='UTF-8' />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,shrink-to-fit=no">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/images/bird.svg">
    <link rel = 'stylesheet' href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css'/>


    <link rel="stylesheet" href="<?= site_url('/assets/css/style-complete.css');?>" />
    <link rel="stylesheet" href="<?= site_url('/assets/css/style.css');?>" />
    <link rel="stylesheet" href="<?= site_url('/assets/css/font-awesome.css');?>" />
    <link rel="stylesheet" href="<?= site_url('/assets/css/bootstrap.css');?>" />
    <script src="<?= site_url('/assets/js/jquery-3.1.1.min.js');?>"></script>

    <script src = 'https://code.jquery.com/jquery-3.2.1.min.js'></script>


</head>

<body>

    <div class="grid-container">
        <!--    <div class='wrapper'>-->

        <?php require 'left-sidebar.php' ?>

        <div class="main">
            <div class=''>
                <div class=''>
                    <!--TWEET WRAPPER-->
                    <p class="page_title mb-0">Home</p>
                    <div class='tweet_box tweet_add'>
                        <div class='left-tweet ml-3'>
                            <!-- PROFILE-IMAGE -->
                            <img class="mr-3" src="<?php // echo $user->profileImage; ?>" style="width: 53px;height:53px;border-radius:50%;" />
                        </div>

                        <script type='text/javascript' src="<?php echo site_url('assets/js/search.js'); ?>"></script>
                        <script type='text/javascript' src="<?php echo site_url('assets/js/hashtag.js'); ?>"></script>

                        <div class='tweet_body'>
                            <form method='post' enctype='multipart/form-data'>
                                <textarea class='status' maxlength='1000' name='status' placeholder="What's happening?" rows='3' cols='100%' style="font-size:17px;"></textarea>
                                <div class='hash-box'>
                                    <ul>
                                    </ul>
                                </div>

                                <div class='tweet_icons-wrapper'>
                                    <div class='t-fo-left tweet_icons-add'>
                                        <ul>
                                            <input type='file' name='file' id='file' />
                                            <li><label for='file'><i class='fa fa-image' aria-hidden='true'></i></label>
                                                <i class="fa fa-bar-chart"></i>
                                                <i class="fa fa-smile-o"></i>
                                                <i class="fa fa-calendar-o"></i>
                                            </li>
                                            <span class='tweet-error'><?php if ( isset( $error ) ) {
                                                echo $error;
                                            } else if ( isset( $imgError ) ) {
                                                echo '<br>' . $imgError;
                                            }
                                            ?></span>
                                            <!--<i class="fa fa-image"></i>-->

                                        </ul>
                                    </div>
                                    <div class='t-fo-right'>
                                        <!--<span id='count'>140</span>-->
                                        <!--<input type='submit' name='tweet' value='tweet' />-->

                                        <button class="button_tweet" type="submit" name="tweet" style="outline:none;">Tweet</button>

                                    </div>
                            </form>
                        </div>
                        <!--</div>-->
                    </div>
                </div>
                <div class="space" style="height:10px; width:100%; background:rgba(230, 236, 240, 0.5);">
                </div>
                <!--TWEET WRAP END-->

                <!--Tweet SHOW WRAPPER-->
                <div class='tweets'>
                    <?php //$getFromT->tweets( $user_id, 20 );
                    ?>
                </div>
                <!--TWEETS SHOW WRAPPER-->

                <div class='loading-div'>
                    <img id='loader' src='assets/images/loading.svg' style='display: none;' />
                </div>
                <div class='popupTweet'></div>
                <!--Tweet END WRAPER-->
                <script type='text/javascript' src="<?php echo site_url('assets/js/like.js'); ?>"></script>
                <script type='text/javascript' src="<?php echo site_url('assets/js/retweet.js'); ?>"></script>
                <script type='text/javascript' src="<?php echo site_url('assets/js/popuptweets.js'); ?>"></script>
                <script type='text/javascript' src="<?php echo site_url('assets/js/delete.js'); ?>"></script>
                <script type='text/javascript' src="<?php echo site_url('assets/js/comment.js'); ?>"></script>
                <script type='text/javascript' src="<?php echo site_url('assets/js/popupForm.js'); ?>"></script>
                <script type='text/javascript' src="<?php echo site_url('assets/js/fetch.js'); ?>"></script>
                <script type='text/javascript' src="<?php echo site_url('assets/js/messages.js'); ?>"></script>
                <script type='text/javascript' src="<?php echo site_url('assets/js/notification.js'); ?>"></script>
                <script type='text/javascript' src="<?php echo site_url('assets/js/postMessage.js'); ?>"></script>

            </div><!-- in left wrap-->
        </div><!-- in center end -->
    </div>



    <?php require 'right-sidebar.php' ?>

    <script type='text/javascript' src="<?php echo site_url('/assets/js/follow.js'); ?>"></script>

    <script src="<?php echo site_url('/assets/js/jquery-3.1.1.min.js'); ?>"></script>
    <script src="<?php echo site_url('/assets/js/popper.min.js'); ?>"></script>
    <script src="<?php echo site_url('/assets/js/bootstrap.min.js'); ?>"></script>

</body>

</html>
