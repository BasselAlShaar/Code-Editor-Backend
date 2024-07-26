<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * an API that registers a user 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function createUser(Request $request){
        $validated_data=$request->validate([
            'email'=>'required',
            'password'=>'required|min:6',
            'name'=>'required|string',
        ]);

        $user = User::insert($validated_data);
        return response()->json([
            'message'=>'user created',
            'user'=>$user,
        ],201);
    }

    /**
     * an API that gets a specific user based on his ID
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getUser($id){
        $user = User::find($id);
        return response()->json([
            'user'=>$user,
        ],200);
    }

    /**
     * an API that retreives all existing users
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getAllUsers(){
        $users = User::all();
        return response()->json([
            'users'=>$users
        ],200);
    }

    /**
     * an API that updates a user based on his ID
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */

    public function updateUser(Request $request,$id){
        $user = User::find($id);
        if($user){
            $validated_data=$request->validate([
                'email'=>'required|email',
                'password'=>'required|min:6',
                'name'=>'required|string',
            ]);

            $user->update($validated_data);
            return response()->json([
                'message'=>'user updated secuessfully'
            ],204);
        }else{
            return response()->json([
                'message'=>'user not found'
            ]);
        }
    }

        /**
         * an API that deletes a user based on his ID
         * @param mixed $id
         * @return mixed|\Illuminate\Http\JsonResponse
         */
        public function deleteUser($id){
            $user = User::find($id);
            $user->delete();
            return response()->json([
                'message'=>'user deleted'
            ]);
        }

    }


