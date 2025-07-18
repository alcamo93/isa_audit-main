<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

include('v2/api/auth.php');
include('v2/api/catalogs/catalogs.php');
include('v2/api/catalogs/guidelines.php');
include('v2/api/catalogs/legalBasi.php');