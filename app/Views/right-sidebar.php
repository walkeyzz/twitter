<div class="right_sidebar">
            <div class="search-container">
                <a href="" class="search-btn">
                    <i class="fa fa-search"></i>
                </a>
                <input type="text" name="search" placeholder="search" class="search-input search" autocomplete="off">
            </div>
            <div class='search-result'>
            </div>

            <?php
            echo '<div class="trends_container"><div class="trends_box"><div class="trends_header"><p>Trends for you</p></div><!-- trend title end-->';
        		foreach ($trends as $trend) {
        			echo '<div class="trends_body">
        					<div class="trend">
                            <span>Trending</span>
        						<p>
        							<a style="color: #000;">#'.$trend->hashtag.'</a>
        						</p>
        						<div class="trend-tweets">

        						</div>
        					</div>
                        </div>
                        <div>
        				</div>';
        		}
        		echo '<div class="trends_show-more">
                            <a href="">Show more</a>
                        </div></div></div>';
            ?>

            <?php
              echo '<div class="trends_container"><div class="trends_box"><div class="trends_header"><p>Who to follow</p></div>';
          		foreach ($whotofollow as $user) {
          			echo '<div class="follow-body trend">
          					<div class="follow-img media-inner">
          					  <img src="'.site_url().$user->profileImage.'"/>
          				    </div>
          					<div class="media-inner">
          						<div class="fo-co-head media-body">
          							<a href="'.site_url().$user->username.'">'.$user->screenName.'</a><br><span>@'.$user->username.'</span>
          						</div>
          						<!-- FOLLOW BUTTON -->
          						'.followBtn($user->user_id, $user_id, $user_id).'
          					</div>
          				</div>';
          		}
          		echo '<div class="trends_show-more">
                              <a href="">Show more</a>
                          </div></div></div>';
            ?>
