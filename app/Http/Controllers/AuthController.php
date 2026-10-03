<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Services\AuthService;

class AuthController extends Controller
{
   
    public function register(Request $request, AuthService $service)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id'  => 'required|exists:roles,id',
            'no_hp'    => 'required|string|max:20', // Validasi untuk nomor HP
        ]);

        try {
            $user = $service->register($request->all());

            return response()->json([
                'message' => 'Registrasi berhasil, OTP telah dikirim ke email',
                'email'   => $user->email,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
public function resendOtp(Request $request, AuthService $service)
    {
        // $request->validate([
        //     'email' => 'required|email|exists:users,email',
        // ]);

        try {
            $user = $service->resendOtp($request->all());

            return response()->json([
                'message' => 'Resend OTP telah dikirim ke email',
                'email'   => $user->email,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // 2️⃣ Ambil user hasil attempt
        $user = auth()->user();

        // 🔴 3️⃣ BLOK jika email belum diverifikasi
        if (is_null($user->email_verified_at)) {
            // invalidate token yang terlanjur dibuat
            //JWTAuth::invalidate($token);

            return response()->json([
                'message' => 'Email belum diverifikasi'
            ], 205);
        }
        return response()->json([
            'id' => auth()->user()->id,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'role' => auth()->user()->role_id,
            'token' => $token,
        ]);
    }
    public function refresh()
    {
        return response()->json(['token' => JWTAuth::refresh()]);
    }
    public function me()
    {
        return response()->json(auth()->user());
    }
    public function getMenus()
    {
        $user = auth()->user();
        $menus = Menu::whereHas('roles', function ($query) use ($user) {
            $query->where('id', $user->role_id);
        })->get();

        return response()->json($menus);
    }
    public function getMenuByRole(Request $request)
    {
        $roleId = $request->role;

        $menus = DB::table('menus')
            ->select(
                'menus.id',
                'menus.title',
                'menus.icon',
                'menus.route',
                'menus.color',
                DB::raw('GROUP_CONCAT(roles.name ORDER BY roles.id SEPARATOR ", ") as requiredRoles')
            )
            ->join('menu_role', 'menus.id', '=', 'menu_role.menu_id')
            ->join('roles', 'roles.id', '=', 'menu_role.role_id')
            ->where('menu_role.role_id', $request->role)
            ->groupBy('menus.id', 'menus.title', 'menus.icon', 'menus.route', 'menus.parent_id', 'menus.color',  'menus.order')
            ->orderBy('menus.order')
            ->get();

        return response()->json($menus);
    }
    public function myMenus()
    {
        $user = auth()->user(); // ✅ JWT

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        if (!$user->role) {
            return response()->json([
                'message' => 'User belum punya role'
            ], 422);
        }

        $menus = $user->role
            ->menus()
            ->orderBy('parent_id')
            ->orderBy('order')
            ->get();

        return response()->json([
            'role'  => $user->role->name,
            'menus' => $menus
        ]);
    }
    public function listRolesWithMenus()
    {
        $roles = Role::with(['menus' => function ($q) {
            $q->orderBy('parent_id')
                ->orderBy('order');
        }])->get();

        return response()->json($roles);
    }
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function verifyOtp(Request $request, AuthService $service)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string',
        ]);

        try {
            $token = $service->verifyOtpAndLogin(
                $request->email,
                $request->otp
            );

            // return response()->json([
            //     'message' => 'Verifikasi berhasil',
            //     'token'   => $token
            // ]);
            // ambil user terbaru
            $user = \App\Models\User::where('email', $request->email)->first();

            return response()->json([
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role_id,
                'token' => $token, // 🔥 INI YANG DIPAKAI AUTO LOGIN
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
