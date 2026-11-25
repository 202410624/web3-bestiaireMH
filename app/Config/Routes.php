<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Accueil
$routes->get('/', 'Home::index');

// Photos
$routes->get('photos', 'Photos::index');
$routes->get('photo/(:num)', 'Photos::show/$1');

// Bestiaire
$routes->get('monster/(:num)', 'Monster::show/$1');

// À propos
$routes->get('about', 'About::index');

// Contact
$routes->get('contact', 'Contact::index');
$routes->post('contact', 'Contact::submit');

// Authentification
$routes->get('login', 'Auth::connexion');
$routes->post('login', 'Auth::traiterConnexion');
$routes->get('register', 'Auth::inscription');
$routes->post('register', 'Auth::traiterInscription');

$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('profil', 'Profile::index');
    $routes->post('monster/(:num)/hunt', 'Monster::recordHunt/$1');
    $routes->post('logout', 'Auth::deconnexion');
});

$routes->group('gestion/monstres', ['filter' => ['auth', 'admin']], static function (RouteCollection $routes): void {
    $routes->get('', 'ManageMonsters::index');
    $routes->get('ajouter', 'ManageMonsters::create');
    $routes->post('ajouter', 'ManageMonsters::store');
    $routes->get('(:num)/modifier', 'ManageMonsters::edit/$1');
    $routes->post('(:num)/modifier', 'ManageMonsters::update/$1');
    $routes->get('(:num)/supprimer', 'ManageMonsters::confirmDelete/$1');
    $routes->post('(:num)/supprimer', 'ManageMonsters::delete/$1');
});
