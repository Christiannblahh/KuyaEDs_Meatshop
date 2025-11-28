<?php

// Front controller wrapper so the app can be accessed at:
// http://localhost/KuyaEDsMeatshop  (without /public)
//
// This simply forwards all requests to the original public front controller.

require __DIR__ . '/public/index.php';


