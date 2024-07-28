<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Code;
use App\Models\User;

class CodeController extends Controller
{
    
    /**
     * an API that saves the user code in the DB
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function createCode(Request $request, $userID){
        $validated_array = $request->validate([
            'title'=>'string|required',
            'content'=>'required',
            'user_id'=>'numeric|required'
        ]);
        $code = Code::insert($validated_array);
        return response()->json([
            'message'=>'code saved',
            'code'=>$code,
        ]);
    }

    /**
     * an API that retreives a specific code saved
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getCode($id){
        $code = Code::get($id);
        return response()->json([
            'code'=>$code,
        ]);
    }

    /**
     * an API that takes a user ID and retreives all codes this user saved
     * @param mixed $userID
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getAllCodes($userID){
        $user = User::find($userID);
        if($user){
            $codes= Code::join('users', 'codes.user_id', '=', 'users.id')
            ->where('codes.user_id', $userID)
            ->get();

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'codes' => $codes,
            ], 200);
        }
        else{
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }
    }


    /**
     * Update a specific code of a user
     * @param \Illuminate\Http\Request $request
     * @param mixed $userID
     * @param mixed $codeID
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function updateCode(Request $request, $userID, $codeID)
    {
        $user = User::find($userID);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $code = Code::where('user_id', $userID)->find($codeID);
        if (!$code) {
            return response()->json([
                'message' => 'Code not found',
            ], 404);
        }

        $validated_data = $request->validate([
            'code' => 'required|string',
        ]);

        $code->update($validated_data);

        return response()->json([
            'message' => 'Code updated successfully',
            'code' => $code,
        ], 200);
    }

    /**
     * Delete a specific code of a user
     * @param mixed $userID
     * @param mixed $codeID
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function deleteCode($userID, $codeID)
    {
        $user = User::find($userID);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $code = Code::where('user_id', $userID)->find($codeID);
        if (!$code) {
            return response()->json([
                'message' => 'Code not found',
            ], 404);
        }

        $code->delete();

        return response()->json([
            'message' => 'Code deleted successfully',
        ], 200);
    }

}

