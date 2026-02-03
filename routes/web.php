<?php

use App\Models\News;
use App\Models\Category;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Customer\TagController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\NewsController;
use App\Http\Controllers\Customer\SearchController;
use App\Http\Controllers\Customer\CategoryController;
// use App\Http\Controllers\Customer\MessageController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Models\User;

// صفحه اصلی سایت
Route::get('/', [HomeController::class, 'home'])->name('customer.home');

// صفحه یک دسته‌بندی خاص
Route::get('/category/{category:title}', [CategoryController::class, 'show'])->name('customer.category.show');

// صفحه یک خبر خاص
Route::get('/news/{news:title}', [NewsController::class, 'show'])->name('customer.news.show');

// صفحه تگ خاص
Route::get('/tags/{tags:name}', [TagController::class, 'show'])->name('customer.news.tags');


// Route::get('/messages/show/{message}', [MessageController::class, 'show'])->name('customer.messages.show');

// Route::prefix('admin')->namespace('Admin')->group(function(){
//     Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.home');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/search', [SearchController::class, 'index'])->name('customer.search');


Route::get('/s/{code}', function ($code) {
    $news = News::where('short_link', $code)->firstOrFail();
    return redirect()->route('customer.news.show', $news);
})->name('short.redirect');


Route::post('/comments', [CommentController::class, 'store'])->name('customer.comments');

// use Illuminate\Support\Facades\Gate;
// Route::get('/test', function () {
//     $user = User::find(1); // فرض کن کاربر با آی‌دی 1 رو داریم
//     dd(Gate::forUser($user)->allows('access-filament'));
// });

require __DIR__.'/auth.php';
