<?php


use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\UserRegistration;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UploadFileController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MessageController;




Route::get('/test', function() {
    return view('test');
    //return view('test',['name'=>'Virat Gandhi']);
    //return response()->file(resource_path('views/test.php'));
 });

 Route::get('/test2', function() {
    return view('test2');
 });

 Route::get('/child', function() {
    return view('child');
 });

 Route::get('/test', ['as'=>'testing',function() {
    return view('test2');
 }]);

 Route::get('redirect',function() {
    return redirect()->route('testing');
 });

/*Route::get('rr','RedirectController@index');
Route::get('/redirectcontroller',function() {
   return redirect()->action('RedirectController@index');
});*/

Route::get('/form',function() {
    return view('form');
 });

 Route::get('session/get', [SessionController::class, 'accessSessionData']);
 Route::get('session/set', [SessionController::class, 'storeSessionData']);
 Route::get('session/remove', [SessionController::class, 'deleteSessionData']);

 Route::get('/uploadfile', [UploadFileController::class, 'index']);
 Route::post('/uploadfile', [UploadFileController::class, 'showUploadFile']);


 // Route để hiển thị trang gallery
 Route::get('/', function () {
   return redirect()->route('gallery');
});
Route::get('/images/gallery', [ImageController::class, 'index'])->name('images.gallery');

//Route::get('/images/new', [CommentController::class, 'show'])->name('images.new');

 //Route::get('/images/gallery', [ImageController::class, 'index']);
 //Route::get('/images', [ImageController::class, 'index'])->name('images.gallery');  // Thêm tên route
 //Route::get('/images', [ImageController::class, 'index']);
 Route::post('/images/upload', [ImageController::class, 'store']);
 Route::get('/images/upload',function () {
    return view('upload');
});
 Route::get('/images/gallery', [ImageController::class, 'show']) -> name('gallery');




 Route::get('/images/edit/{id}', [ImageController::class, 'edit'])->name('images.edit');
 Route::post('/images/update/{id}', [ImageController::class, 'update'])->name('images.update');
 Route::delete('/images/delete/{id}', [ImageController::class, 'destroy'])->name('images.delete');

 Route::get('/login',[AuthManager::class, 'login']) ->name('login');
 Route::post('/login',[AuthManager::class, 'loginPost']) ->name('login.post');
 Route::get('/registration',[AuthManager::class, 'registration']) ->name('registration');
 Route::post('/registration',[AuthManager::class, 'registrationPost']) ->name('registration.post');
 Route::get('/logout', [AuthManager::class,'logout']) ->name('logout');
 Route::post('/logout', [AuthManager::class, 'logout'])->name('logout');

 Route::post('/images/store', [ImageController::class, 'store'])->middleware('auth')->name('images.store');

 //chức năng tìm kiếm//
 Route::get('/images/search', [ImageController::class, 'search'])->name('images.search');

 //route show ảnh
 Route::get('/images/{id}', [ImageController::class, 'showDetail'])->name('images.showDetail');

 Route::get('/profile', [UserController::class, 'showProfileIfAuth'])->name('profile.show')->middleware('auth');
 Route::get('/profile/{imageId}', [UserController::class, 'showProfile'])->name('profile.show2');
 Route::get('/users/{userId}', [UserController::class, 'showUserProfile'])->name('profile.showUser');


 Route::get('/test', function() {
   return view('chat');
});



 Route::middleware(['auth'])->group(function () {
     Route::get('/chat-rooms', [ChatController::class, 'getChatRooms']);
     Route::post('/create-room', [ChatController::class, 'createChatRoom'])->name('room.create');
     Route::get('/messages/{roomId}', [ChatController::class, 'getMessages']);
     Route::post('/messages', [ChatController::class, 'sendMessage']); // Thêm API lưu tin nhắn
     Route::post('/messages/image-upload', [MessageController::class, 'uploadImage']);

     Route::get('/chatBox', [ChatController::class, 'index'])->name('chat.index');
     Route::get('/chat-rooms/search-by-artist-name', [ChatController::class, 'searchRoomsByArtistName'])->name('chat.rooms.searchByArtistName');
});

 //Route::get('/profile/collections', [CollectionController::class, 'index'])->middleware('auth')->name('collections.index');

// Route::middleware('auth')->group(function () {
  // Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
//Route::post('/collections', [CollectionController::class, 'store']);
//Route::get('/collections/user', [CollectionController::class, 'userCollections']);
//});

// Add these routes to your routes/web.php file
//Route::get('/images/gallery', [ImageController::class, 'showGallery'])->name('images.gallery');
// Collection routes
Route::prefix('collections')->group(function () {
   Route::get('/', [CollectionController::class, 'index'])->name('collections.index'); // cái này cần
   Route::get('/user/{userId}', [CollectionController::class, 'index'])->name('collections.user');
   Route::get('/create', [CollectionController::class, 'create'])->name('collections.create');
   Route::post('/', [CollectionController::class, 'store'])->name('collections.store');
   Route::get('/{id}', [CollectionController::class, 'show'])->name('collections.show');
   Route::get('/{id}/edit', [CollectionController::class, 'edit'])->name('collections.edit');
   Route::put('/{id}', [CollectionController::class, 'update'])->name('collections.update');
   Route::delete('/{id}', [CollectionController::class, 'destroy'])->name('collections.destroy');

   // Add/remove images from collections
   Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store');
   Route::post('/add-image', [CollectionController::class, 'addImage'])->name('collections.addImage');
   Route::delete('/{collectionId}/images/{imageId}', [CollectionController::class, 'removeImage'])->name('collections.removeImage');
   Route::put('/{collectionId}/order', [CollectionController::class, 'updateImageOrder'])->name('collections.updateImageOrder');

   // Select collection for adding image

   Route::get('/images/{id}', [ImageController::class, 'showDetail'])->name('images.show');
   Route::get('/select/{imageId}', [CollectionController::class, 'selectCollection'])->name('collections.select');
   Route::post('/images/{id}/add-to-collection', [App\Http\Controllers\ImageController::class, 'addToCollection'])->name('images.addToCollection');
   // Get user collections (AJAX)
   Route::get('/user-collections', [CollectionController::class, 'getUserCollections'])->name('collections.getUserCollections');
   Route::get('/list', [CollectionController::class, 'list'])->name('collections.list');
});
