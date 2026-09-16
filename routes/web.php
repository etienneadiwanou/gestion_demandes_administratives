<?php

use Illuminate\Support\Facades\Route;

// Toute route qui n'est pas /api/... sert la même page HTML : c'est
// Vue Router (mode history) qui prend le relais côté client.
// Cette route doit rester la DERNIÈRE définie dans ce fichier.
Route::view('/{any}', 'app')->where('any', '.*');
