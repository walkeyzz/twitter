<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

 // All frontend requests will be routed through here
$routes->group('', ['namespace' => 'App\Controllers\Frontend'], function($routes) {
  $routes->get('/', 'Home::index');

  $routes->get('login', 'Login::index');
  $routes->post('signin', 'Login::signin');
  $routes->get('logout', 'Login::logout');

  $routes->post('signup', 'Login::signup');
  $routes->get('signup/(:num)', 'Login::signup/$1');
  $routes->post('signup/next', 'Login::next');

  $routes->get('profile/(:any)', 'Profile::index/$1');
  $routes->get('settings/account', 'Settings::account');
});
