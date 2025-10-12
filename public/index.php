<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\SimpleUserProvider;
use BaseApi\App;

// Boot the application
App::boot(__DIR__ . '/..');
App::setUserProvider(new SimpleUserProvider());

// Load routes
require_once __DIR__ . '/../routes/api.php';

// Handle the request
App::kernel()->handle();
