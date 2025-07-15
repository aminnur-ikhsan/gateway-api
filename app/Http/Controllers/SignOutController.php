<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\AccessTokenModel;

class SignOutController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();
            // Hapus semua token pengguna saat ini
            // Metode ini mengakses relasi token secara langsung
            DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();
        }

        return $this->responseSuccess('Successfully signed out');
    }

    public function indexProvider(Request $request)
    {
        $token = $request->bearerToken();

        // Hapus semua token pengguna dengan satu query
        AccessTokenModel::whereIn('id_user', function ($query) use ($token) {
            $query->select('id_user')
                ->from('custom.provider_access_tokens')
                ->where('token', $token);
        })->delete();

        return $this->responseSuccess('Successfully signed out');
    }
}
