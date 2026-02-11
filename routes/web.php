<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PoktanController;
use App\Http\Controllers\PejabatController;
use App\Http\Controllers\SptController;
use App\Http\Controllers\RekapSuratKeluarController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
| Kalau belum login -> login
| Kalau sudah login -> home (/index)
*/

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('home')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD (Breeze default)
|--------------------------------------------------------------------------
| Breeze default redirect ke /dashboard
| kita arahkan ke /index
*/
Route::middleware('auth')->get('/dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| SEMUA FITUR (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    | Dashboard utama aplikasi
    */
    Route::get('/index', [DashboardController::class, 'index'])->name('home');

    /*
    | MASTER DATA
    */
    Route::resource('petugas', PetugasController::class);
    Route::resource('poktan', PoktanController::class);
    Route::resource('pejabat', PejabatController::class);

    /*
    | SPT
    */
    Route::resource('spt', SptController::class)->except(['show']);

    // Print:
    // - /spt/print        -> print all (id null)
    // - /spt/print/123    -> print single
    Route::get('spt/print/{id?}', [SptController::class, 'print'])->name('spt.print');

    /*
    | REKAP SURAT KELUAR
    */
    Route::prefix('rekap-surat-keluar')->name('rekap-surat-keluar.')->group(function () {
        Route::get('/', [RekapSuratKeluarController::class, 'index'])->name('index');
        Route::get('/print', [RekapSuratKeluarController::class, 'print'])->name('print');
    });

    /*
    | ACTIVITY
    */
    Route::prefix('activity')->name('activity.')->group(function () {
        Route::get('/navbar', [ActivityController::class, 'navbar'])->name('navbar');
        Route::match(['POST', 'DELETE'], '/clear', [ActivityController::class, 'clear'])->name('clear');
    });

    // Alias supaya tidak error kalau ada pemanggilan route lama
    Route::match(['POST', 'DELETE'], '/activities/destroy-all', [ActivityController::class, 'clear'])
        ->name('activities.destroyAll');

    /*
    | SEARCH (LIVE SEARCH)
    | IMPORTANT:
    | - route('search')        -> untuk kompatibel dengan menu lama kamu
    | - route('search.index')  -> versi rapi
    | - route('search.results')-> endpoint JSON
    */
    Route::get('/search', [SearchController::class, 'index'])->name('search'); // ✅ FIX ERROR Route [search] not defined

    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/', [SearchController::class, 'index'])->name('index');     // /search
        Route::get('/results', [SearchController::class, 'results'])->name('results'); // /search/results
    });

    /*
    | IMPORT
    */
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('/', [ImportController::class, 'index'])->name('index');
        Route::post('/petugas', [ImportController::class, 'importPetugas'])->name('petugas');
        Route::post('/poktan', [ImportController::class, 'importPoktan'])->name('poktan');
        Route::post('/spt', [ImportController::class, 'importSpt'])->name('spt');
    });

    /*
    | PROFILE
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (BREEZE)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
