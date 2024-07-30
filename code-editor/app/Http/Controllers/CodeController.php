<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Code;
use JWTAuth;

class CodeController extends Controller
{
    /**
     * Save the user code in the DB
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCode(Request $request)
    {
        // Retrieve the authenticated user
        $user = JWTAuth::parseToken()->authenticate();

        $validated_array = $request->validate([
            'title' => 'string|required',
            'content' => 'required',
        ]);

        // Add the authenticated user's ID to the validated data
        $validated_array['user_id'] = $user->id;

        $code = Code::create($validated_array); // Use create for returning the created model

        return response()->json([
            'message' => 'Code saved',
            'code' => $code,
        ]);
    }

    /**
     * Retrieve a specific code saved by the authenticated user
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCode($id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $code = Code::where('user_id', $user->id)->find($id);
        if (!$code) {
            return response()->json([
                'message' => 'Code not found',
            ], 404);
        }

        return response()->json([
            'code' => $code,
        ]);
    }

    /**
     * Retrieve all codes saved by the authenticated user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCodes()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $codes = Code::where('user_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'codes' => $codes,
        ], 200);
    }

    /**
     * Update a specific code of the authenticated user
     * @param \Illuminate\Http\Request $request
     * @param mixed $codeID
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCode(Request $request, $codeID)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $code = Code::where('user_id', $user->id)->find($codeID);
        if (!$code) {
            return response()->json([
                'message' => 'Code not found',
            ], 404);
        }

        $validated_data = $request->validate([
            'title' => 'sometimes|string',
            'content' => 'sometimes|string',
        ]);

        $code->update($validated_data);

        return response()->json([
            'message' => 'Code updated successfully',
            'code' => $code,
        ], 200);
    }

    /**
     * Delete a specific code of the authenticated user
     * @param mixed $codeID
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCode($codeID)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $code = Code::where('user_id', $user->id)->find($codeID);
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
