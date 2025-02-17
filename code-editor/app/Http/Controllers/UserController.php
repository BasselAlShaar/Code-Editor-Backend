<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Register a new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUser(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'name' => 'required|string',
        ]);

        // Hash the password before saving
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Create the user
        $user = User::create($validatedData);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }

    /**
     * Get a specific user by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'user' => $user,
        ], 200);
    }

    /**
     * Retrieve all existing users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers()
    {
        $users = User::all();
        return response()->json([
            'users' => $users,
        ], 200);
    }

    /**
     * Update a specific user by ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();


        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'name' => 'required|string',
        ]);

        // Hash the password if it is being updated
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ], 200);
    }

    /**
     * Delete a specific user by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ], 200);
    }

    public function updateUserPassword(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        if(!$user){
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
        else{
            $request->validate([
            'password' => 'required|string|min:6',
            ]);
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json(['message' => 'Password updated successfully']);
        }
        
    }

    public function importUsers(Request $request)
{
    // Store the uploaded file temporarily
    $filePath = $request->file('file')->store('temp');

    try {
        // Use Excel import
        Excel::import(new UsersImport, storage_path('app/' . $filePath));

        // Return success response
        return response()->json(['success' => true, 'message' => 'Users imported successfully!']);
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        // Handle validation exceptions
        $failures = $e->failures();
        return response()->json(['success' => false, 'message' => 'Validation errors occurred', 'errors' => $failures]);
    } catch (\Exception $e) {
        // Handle all other exceptions
        return response()->json(['success' => false, 'message' => 'Error importing users: ' . $e->getMessage()]);
    }
}

}
