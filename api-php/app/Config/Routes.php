<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/test', 'ValidationForm::test');
$routes->post('/validation_form/get_text_from_id_card', 'ValidationForm::get_text_from_id_card');
$routes->post('/validation_form/compare_idcard_with_selfie', 'ValidationForm::compare_idcard_with_selfie');
$routes->post('/validation_form/submit_form', 'ValidationForm::submit_form');
$routes->post('/validation_form/check_cnp', 'ValidationForm::check_cnp');

// $routes->post('/upload_buletin', 'ValidationForm::upload_buletin');
// $routes->post('/upload_selfie', 'ValidationForm::upload_selfie');
// $routes->post('/save_form', 'ValidationForm::save_form');
// $routes->post('/decide_request', 'ValidationForm::decide_request');
$routes->post('/send_email', 'ValidationForm::send_email');

$routes->post('/admin/login', 'Admin::login');
$routes->post('/admin/register', 'Admin::register');
$routes->post('/admin/getAllRequests', 'Admin::getAllRequests');

$routes->post('/evuid_generator/check_token', 'EvuidGenerator::check_token');
$routes->post('/evuid_generator/send_email_voting_paper', 'EvuidGenerator::send_email_voting_paper');

$routes->post('/vote/get_candidates', 'Vote::get_candidates');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
