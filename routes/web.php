<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\ManagerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlanogramController;
use App\Http\Controllers\Dashboard\ShowcaseController as DashboardShowcaseController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShowcaseController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\UploadController;
use App\Http\Middleware\IsAdmin;
use App\Jobs\ProductSyncJob;
use App\Models\Group;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



Route::get('/archives/{code}/{year}/{month}', function(Request $request){
    $products = Product::where('Code',$request->code)->orWhere('Code','2000009656')->with(['archives' => function($query) use ($request){
        $query->where('year',$request->year)->where('month',$request->month);
    }])->get();
    dump($products);
});

Route::get('/sync-archives/{shop}/{year}/{month}', function(Request $request){
    Artisan::call('app:sync-archives', [
        'shop' => $request->shop,
        'year' => $request->year,
        'month' => $request->month,
    ]);
});

Route::get('/img/{code}', function(Request $request){
    dump(public_path());
   dump(\App\Models\Barcode::exists($request->code));
});

Route::get('/update-admin', function(){
   $user = User::where('login','admin')->first();

   if($user){
       $user->password = Hash::make('secret123!');
       $user->save();
   }
});

/*-----------------------------------------------------------------------------------*/

Route::post('/login', [AuthController::class, 'login'])->name('login.store');
//Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('planogram/{id}', PlanogramController::class)->name('planogram');
    Route::get('/', HomeController::class)->name('home');

    Route::controller(ShowcaseController::class)->group(function () {
        Route::post('/showcase/{shop_id}', 'store')->name('showcase.store');
        Route::post('/showcase/{shop_id}/duplicate', 'duplicate')->name('showcase.duplicate');
        Route::put('/showcase/{id}', 'update')->name('showcase.update');
        Route::delete('/showcase/{id}/remove', 'destroy')->name('showcase.remove');
        Route::delete('/shelves/{id}/remove', 'destroyShelves')->name('shelves.remove');
    });
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard/Index');
    })->name('dashboard');

    Route::middleware([
        IsAdmin::class
    ])->prefix('dashboard')->group(function () {
        Route::resources([
            // 'showcases' =>          DashboardShowcaseController::class,
            'comments' =>           CommentController::class,
            'managers' =>           ManagerController::class,
            'admins' =>             AdminController::class,
            // 'shops' =>              ShopController::class,
            'users' =>              UserController::class,
            'products' =>              ProductController::class,
        ]);
    });
    
    Route::post('comments/send', [CommentController::class, 'send'])->name('send.comment');
    Route::post('shops/update-products/{c_id}', [ShopController::class, 'updateProducts'])->name('shops.store.update');
    Route::get('shops/get-products/{c_id}', [ShopController::class, 'getProducts'])->name('shops.get.products');
    Route::get('shops/get-subgroup/{group_key}', [ShopController::class, 'getSubgroup'])->name('shops.get.subGroup');
    Route::post('products/shelves/{showcase}', [ProductController::class, 'storeShelvesProducts'])->name('store.shelves.products');
    Route::get('showcase-comments/{id}', [ShowcaseController::class, 'showcaseComments'])->name('showcase.comments');
    Route::get('showcase.images/{id}', [ShowcaseController::class, 'showcaseImages'])->name('showcase.images');
    Route::post('upload-image/{id}', UploadController::class)->name('upload.image');
    Route::delete('remove-showcase-image/{id}', [ShowcaseController::class, 'removeImageFromShowcase'])->name('remove.showcase.image');
    

    Route::resources([
        'showcases' =>          DashboardShowcaseController::class,
        'shops' =>              ShopController::class,
    ]);

});

