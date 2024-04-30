<!doctype html>
  <html>
  	<head>
  		<title>tweety</title>
  		<meta charset="UTF-8" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css"/>
        <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
   		<link rel="stylesheet" href="<?= site_url('/assets/css/style-complete.css'); ?>"/>
   		<link rel="stylesheet" href="<?= site_url('/assets/css/font-awesome.css'); ?>" />
  	</head>
  	<!--Helvetica Neue-->
  <body>
  <div class="wrapper">
  <!-- nav wrapper -->
  <div class="nav-wrapper">

  	<div class="nav-container">
  		<div class="nav-second">
  			<ul>
  				<li><a href="#"><i class="fa fa-twitter" aria-hidden="true"style="color:white;"></i></a></li>
  			</ul>
  		</div><!-- nav second ends-->
  	</div><!-- nav container ends -->

  </div><!-- nav wrapper end -->

  <!---Inner wrapper-->
  <div class="inner-wrapper">
  	<!-- main container -->
  	<div class="main-container">
  		<!-- step wrapper-->
    <?php if ($step == '1') {?>
   		<div class="step-wrapper">
  		    <div class="step-container">
          <?= form_open('signup/next', ['autocomplete' => 'off']); ?>
  					<h2>Choose a Username</h2>
  					<h4>Don't worry, you can always change it later.</h4>
  					<div class="form-group">
  						<input class="form-control"type="text" name="username" placeholder="Username" style="font-size: 16px;"/>
  					</div>

            <?php if (isset($error)) : ?>
            <div>
              <ul>
                  <li><?= $error; ?></li>
              </ul>
            </div>
            <?php endif; ?>

  					<div>
  						<input type="submit" name="next" value="Next"/>
  					</div>
  				 <?= form_close(); ?>
  			</div>
  		</div>
    <?php } ?>
    <?php if ($step == '2'){?>
  	<div class='lets-wrapper'>
  		<div class='step-letsgo'>
  			<h1>We're glad you're here, <?php echo $user->screenName; ?> </h1>
  			<p style="font-size:22px;">Tweety is a constantly updating stream of the coolest, most important news, media, sports, TV, conversations and more--all tailored just for you.</p>
  			<br>
  			<p style="font-size:22px;">
  				Tell us about all the stuff you love and we'll help you get set up.
  			</p>
  			<span>
  				<a href="<?= site_url(); ?>" class='backButton' style="color:var(--twitter-color);">Let's go!</a>
  			</span>
  		</div>
  	</div>
  <?php } ?>

  	</div><!-- main container end -->

  </div><!-- inner wrapper ends-->
  </div><!-- ends wrapper -->

  </body>
  </html>
