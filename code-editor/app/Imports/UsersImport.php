<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\ValidationException;

class UsersImport implements ToModel, WithValidation
{
    public function model(array $row)
    {
        $data = [
            'name' => $row[0],
            'email' => $row[1],
            'password' => $row[2],
        ];

        // Validate the data
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // If validation fails, throw an exception
        if ($validator->fails()) {
            $errors = $validator->errors();
            throw new ValidationException($validator, response()->json([
                'success' => false,
                'message' => 'Validation failed for one or more rows.',
                'errors' => $errors->toArray()
            ], 422));
        }

        // If validation passes, create a new user
        return new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => 'required|string|max:255', // name
            '1' => 'required|email|unique:users,email', // email
            '2' => 'required|string|min:6', // password
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'Name is required',
            '1.required' => 'Email is required',
            '1.email' => 'Email must be a valid email address',
            '1.unique' => 'Email already exists',
            '2.required' => 'Password is required',
            '2.min' => 'Password must be at least 6 characters',
        ];
    }
}
