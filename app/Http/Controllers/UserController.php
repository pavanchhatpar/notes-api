<?php
/**
 * Created by PhpStorm.
 * User: pavan
 * Date: 18/12/2016
 * Time: 11:43 AM
 */

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        return $this->ok(User::all());
    }

    /**
     * Display a listing of the users.
     *
     * @param integer $id
     * @return \Illuminate\Http\Response
     */
    public function find($id) {
        try {
            return User::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return $this->notFound(['message'=>'This user does not exist.']);
        }
    }

    /**
     * Store a new user
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function makeNewUser(Request $request) {
        $this->validate($request, [
            'email'    => 'required',
            'password' => 'required',
            'name'     => 'required'
        ]);

        $credentials = [
            'email'    => $request->email,
            'password' => (new BcryptHasher())->make($request->password),
            'name'     => $request->name
        ];

        if(sizeof(User::where('email', $credentials['email'])->get()->toArray()) !== 0) {
            return $this->conflict(['message' => 'That email address is already signed up']);
        }
        while(sizeof(User::where('salt',$salt = base64_encode(random_bytes(128)))->get()) !== 0);
        $user = new User;

        $user->email    = $credentials['email'];
        $user->password = $credentials['password'];
        $user->name     = $credentials['name'];
        $user->salt     = $salt;

        if($user->saveOrFail()) {
            return $this->created($user);
        } else {
            return $this->serverError(['message' => 'Something went wrong :(']);
        }
    }
}