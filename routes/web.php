<?php

use App\Http\Controllers\Web\ServerStatusPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', ServerStatusPageController::class)->name('server-status.page');
