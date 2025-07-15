<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserProviderModel as UserProvider;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            ]
        ], [
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Format password tidak valid',
            'password.regex' => 'Format password tidak valid'
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->first());
        } else {
            return $this->registration($email, $password);
        }
    }

    public function registration($email, $password)
    {
        try {
            // create unique id user by uuid
            $idUser = Str::uuid();
            for ($i = 0; $i < 100; $i++) {
                // Check if idUser exists in database
                $exists = User::where('id_user', $idUser)->exists();

                if (!$exists) {
                    break; // Exit loop if ID is unique
                }

                // Generate new UUID if ID exists
                $idUser = Str::uuid();
            }

            // Create new user with hashed password
            $newUser = new User();
            $newUser->id_user = (string) $idUser;
            $newUser->name = strstr($email, '@', true);
            $newUser->email = $email;
            $newUser->password = Hash::make($password);
            $newUser->save();

            return $this->responseSuccess('Registration successful', ['user' => $newUser]);
        } catch (\Exception $e) {
            return $this->responseError('Registration failed: ' . $e->getMessage());
        }
    }

    public function indexProvider(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $idProvider = $request['id_provider'];

        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            ]
        ], [
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Format password tidak valid',
            'password.regex' => 'Format password tidak valid'
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->first());
        }

        $isExist = UserProvider::where('id_provider', $idProvider)
            ->where('email', $email)
            ->exists();

        if ($isExist) {
            return $this->responseError('Email sudah terdaftar');
        }

        return $this->registUserProvider($email, $password, $idProvider);
    }

    public function registUserProvider($email, $password, $idProvider)
    {
        try {
            // create unique id user by uuid
            $idUser = Str::uuid();
            for ($i = 0; $i < 100; $i++) {
                // Check if idUser exists in database
                $exists = UserProvider::where('id_user', $idUser)->exists();

                if (!$exists) {
                    break; // Exit loop if ID is unique
                }

                // Generate new UUID if ID exists
                $idUser = Str::uuid();
            }

            // Create new user with hashed password
            $newUser = new UserProvider();
            $newUser->id_user = (string) $idUser;
            $newUser->id_provider = $idProvider;
            $newUser->name = strstr($email, '@', true);
            $newUser->email = $email;
            $newUser->password = Hash::make($password);
            $newUser->save();

            return $this->responseSuccess('Registration successful', ['user' => $newUser]);
        } catch (\Exception $e) {
            return $this->responseError('Registration failed: ' . $e->getMessage());
        }
    }
}
