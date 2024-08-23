
<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlaylistController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();
Route::middleware(['auth'])->group(function () {

    //usuario
    Route::get('home',[HomeController::class, 'index'])
    ->name('user.home');
    Route::post('home',[HomeController::class, 'index'])
    ->name('user.home');

    Route::get('/historico', function () {
        return view('users.historico');
    })->name('user.user');

    Route::post('/historico', function () {
        return view('users.historico');
    })->name('user.historico');


    //rota admin
    Route::middleware(['is_admin'])->group(function () {
        Route::get('/dashboard', [HomeController::class, 'dashboard'])
            ->name('dashboard');
            
        //users

        Route::get('/users', function () {
            return view('users.index');
        })->name('admin.user');

        Route::post('/users', function () {
            return view('users.index');
        })->name('admin.user');
        
        //categorias

        Route::get('/categorias', function () {
            return view('categorias.index');
        })->name('admin.categorias');

        Route::post('/categorias', function () {
            return view('categorias.index');
        })->name('admin.categorias');

        //produtos

        Route::get('/produtos', function () {
            return view('produtos.index');
        })->name('admin.produtos');

        Route::post('/produtos', function () {
            return view('produtos.index');
        })->name('admin.produtos');

        //vendas

        Route::get('/vendas', function () {
            return view('vendas.index');
        })->name('admin.vendas');

        Route::post('/vendas', function () {
            return view('vendas.index');
        })->name('admin.vendas');
    });
});
