<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/*
 * Methods that don't require OAuth 2.0 go here
 */
$app->group(['middleware' => 'client-credentials'], function() use($app) {
    /*
     * All GET methods go here
     */

    /*
     * All POST methods go here
     */
    $app->post('/signup', 'UserController@makeNewUser');
    /*
     * All PUT methods go here
     */

    /*
     * All DELETE methods go here
     */
});

/*
 * All routes which require to be OAuth 2.0 protected go in this group
 */
$app->group(['middleware' => 'auth:api'], function() use($app) {
    $app->get('/', function (Request $request)  {
        return response()->json($request->user());
    });

    /*
     * All GET methods go here
     */
    $app->get('/users', 'UserController@all');
    $app->get('/user/{id}', 'UserController@find');
    $app->get('/notes', 'NoteController@all');
    $app->get('/note/{id}', 'NoteController@find');

    /*
     * All POST methods go here
     */
    $app->post('/note', 'NoteController@makeNewNote');

    /*
     * All PUT methods go here
     */
    $app->put('/note/{id}', 'NoteController@updateThisNote');

    /*
     * All DELETE methods go here
     */
    $app->delete('/note/{id}', 'NoteController@deleteThisNote');
});
