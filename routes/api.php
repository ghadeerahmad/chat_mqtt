<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdvertisementController as AdminAdvertisementController;
use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BackgroundController;
use App\Http\Controllers\admin\BlacklistController;
use App\Http\Controllers\admin\CountryController;
use App\Http\Controllers\admin\ReservedIdController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\RoomController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\api\AdvertisementController;
use App\Http\Controllers\api\ChatController;
use App\Http\Controllers\api\CountryController as ApiCountryController;
use App\Http\Controllers\api\FavouriteController;
use App\Http\Controllers\api\FriendController;
use App\Http\Controllers\api\MessageController;
use App\Http\Controllers\api\RoomBackgroundController;
use App\Http\Controllers\api\RoomBlacklistController;
use App\Http\Controllers\api\RoomController as ApiRoomController;
use App\Http\Controllers\api\RoomPrivilegeController;
use App\Http\Controllers\api\RoomRoleController;
use App\Http\Controllers\api\SearchRoomController;
use App\Http\Controllers\api\SearchUserController;
use App\Http\Controllers\api\UserBlacklistController;
use App\Http\Controllers\api\UserController as ApiUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
/**countries */
Route::get('/countries', ApiCountryController::class);
Route::prefix('admin')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        /** admins */
        Route::get('/admins', [AdminController::class, 'index']);
        Route::get('/admins/{id}', [AdminController::class, 'show']);
        Route::post('/admins', [AdminController::class, 'create']);
        Route::post('/admins/{id}', [AdminController::class, 'update']);
        Route::delete('/admins/{id}', [AdminController::class, 'delete']);
        /**roles */
        Route::get('/roles', [RoleController::class, 'index']);
        /** users */
        Route::get('/users', [UserController::class, 'index']);

        Route::middleware('adminRole:update_user')->group(function () {
            Route::put('/users', [UserController::class, 'update']);
        });
        Route::middleware('adminRole:view_room')->group(function () {
            Route::get('/rooms', [RoomController::class, 'index']);
            Route::get('/rooms/{id}', [RoomController::class, 'show']);
        });
        Route::middleware('adminRole:update_room')->group(function () {
            Route::post('/rooms/{id}', [RoomController::class, 'update']);
        });
        Route::middleware('adminRole:create_room')->group(function () {
            Route::post('/rooms', [RoomController::class, 'create']);
        });
        Route::delete('/rooms/{id}', [RoomController::class, 'delete']);
        /** Advertisments links */
        Route::get('/advertisments', [AdminAdvertisementController::class, 'index']);
        Route::post('/advertisments', [AdminAdvertisementController::class, 'create']);
        Route::delete('/advertisments/{id}', [AdminAdvertisementController::class, 'delete']);
        /** reserved ids */
        Route::post('/reserved_ids', [ReservedIdController::class, 'create']);
        Route::post('/reserved_ids/{id}', [ReservedIdController::class, 'update']);
        Route::get('/reserved_ids', [ReservedIdController::class, 'index']);
        Route::delete('/reserved_ids/{id}', [ReservedIdController::class, 'delete']);
        Route::post('/assign_id', [ReservedIdController::class, 'assign_to_user']);
        /** Countries links */
        Route::get('/countries', [CountryController::class, 'index']);
        Route::post('/countries', [CountryController::class, 'create']);
        Route::post('/countries/{id}', [CountryController::class, 'update']);
        Route::get('/countries/{id}', [CountryController::class, 'show']);

        /** backgrounds */
        Route::apiResource('backgrounds', BackgroundController::class);
        Route::post('/backgrounds/set_default', [BackgroundController::class, 'set_default']);

        /** blacklist */
        Route::apiResource('blacklists', BlacklistController::class);
    });
});

/**app routes */
Route::post('/login', [ApiUserController::class, 'login']);
Route::post('/users/exists', [ApiUserController::class, 'is_exist']);
Route::post('/login_remember', [ApiUserController::class, 'login_remember']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/update_profile', [ApiUserController::class, 'update']);
    Route::get('/users/{id}', [ApiUserController::class, 'profile']);
    Route::get('/users', [ApiUserController::class, 'my_profile']);
    //rooms links
    Route::post('/rooms/create', [ApiRoomController::class, 'create']);
    Route::post('/rooms', [ApiRoomController::class, 'joinRoom']);
    Route::post('/rooms/{id}', [ApiRoomController::class, 'update']);
    Route::get('/rooms', [ApiRoomController::class, 'top']);
    Route::delete('/rooms/{id}', [ApiRoomController::class, 'delete']);
    Route::delete('/rooms', [ApiRoomController::class, 'leaveRoom']);
    Route::post('/rooms/messages/send', [ApiRoomController::class, 'sendMessage']);
    Route::get('/rooms/users/{id}', [ApiRoomController::class, 'users']);
    Route::post('/rooms/users/kick', [ApiRoomController::class, 'kick']);
    Route::post('/rooms/users/ban', [ApiRoomController::class, 'ban']);
    Route::delete('room_roles', [RoomRoleController::class, 'destroy'])->name('room_roles');
    Route::get('/room_privileges', RoomPrivilegeController::class);
    Route::apiResource('room_roles', RoomRoleController::class);
    Route::get('/rooms/filter/{id}', [ApiRoomController::class, 'filter']);
    /** room black list */
    Route::get('/rooms/blacklist/{id}', [RoomBlacklistController::class, 'index']);
    Route::post('/rooms/blacklist/remove', [RoomBlacklistController::class, 'remove']);
    /**advertisements links */
    Route::get('/ads', [AdvertisementController::class, 'index']);
    Route::post('/logout', [ApiUserController::class, 'logout']);
    Route::apiResource('friends', FriendController::class);

    /** chats */
    Route::apiResource('chats', ChatController::class);
    Route::apiResource('chats/messages', MessageController::class);
    Route::post('chats/messages/bulk_delete', [MessageController::class, 'bulk_delete']);
    /** room background */
    Route::get('/room_backgrounds', [RoomBackgroundController::class, 'index']);
    Route::post('/room_backgrounds', [RoomBackgroundController::class, 'select']);

    /** blacklist */
    Route::apiResource('blacklist', UserBlacklistController::class);

    /** favourite */
    Route::apiResource('favourite', FavouriteController::class);

    /** search rooms */
    Route::get('/search/rooms', SearchRoomController::class);

    /** search users */
    Route::get('/search/users', SearchUserController::class);
});
Route::get('/test', [ApiUserController::class, 'test']);
