<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

 // All frontend requests will be routed through here
$routes->group('', ['namespace' => 'App\Controllers\Frontend'], function($routes) {
  $routes->get('/', 'HomeController::index');

  $routes->get('login', 'LoginController::index');
  $routes->post('signin', 'LoginController::signin');
  $routes->get('logout', 'LoginController::logout');

  $routes->post('signup', 'LoginController::signup');
  $routes->get('signup/(:num)', 'LoginController::signup/$1');
  $routes->post('signup/next', 'LoginController::next');

  $routes->post('tweet/submit', 'TweetController::submit');

  $routes->get('profile/(:any)', 'ProfileController::index/$1');
  $routes->get('profileedit', 'ProfileController::edit');
  $routes->post('profileedit/submit', 'ProfileController::submit');
  $routes->post('profileedit/profilecoverimage', 'ProfileController::editCover');
  $routes->post('profileedit/profileimage', 'ProfileController::editProfileImage');

  $routes->get('settings/account', 'SettingsController::index');
  $routes->post('settings/account/submit', 'SettingsController::submit');
  $routes->get('settings/password', 'SettingsController::password');
  $routes->post('settings/password/submit', 'SettingsController::passwordSubmit');

  $routes->get('(:any)/following', 'FollowController::following/$1');
  $routes->get('(:any)/followers', 'FollowController::followers/$1');

  $routes->get('notifications', 'NotificationsController::index');

  $routes->post('ajax/addtweet', 'AjaxController::addTweet');
  $routes->post('ajax/comment', 'AjaxController::comment');
  $routes->post('ajax/deletecomment', 'AjaxController::deleteComment');
  $routes->post('ajax/deletetweet', 'AjaxController::deleteTweet');
  $routes->post('ajax/fetchposts', 'AjaxController::fetchPosts');
  $routes->post('ajax/follow', 'AjaxController::follow');
  $routes->post('ajax/gethashtag', 'AjaxController::getHashtag');
  $routes->post('ajax/imagepopup', 'AjaxController::imagePopup');
  $routes->post('ajax/like', 'AjaxController::like');
  $routes->post('ajax/messages', 'AjaxController::messages');
  $routes->get('ajax/notification', 'AjaxController::notification');
  $routes->post('ajax/popuptweets', 'AjaxController::popupTweets');
  $routes->post('ajax/retweet', 'AjaxController::retweet');
  $routes->post('ajax/search', 'AjaxController::search');
  $routes->post('ajax/searchuserinmsg', 'AjaxController::searchUserinMsg');
  $routes->post('ajax/tweetform', 'AjaxController::tweetForm');
});
