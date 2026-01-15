<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class Auth extends Controller
{
    // 1. Відправка коду
    public function sendCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->email;
        $code = rand(100000, 999999);

        DB::table('otp_codes')->updateOrInsert(
            ['email' => $email],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(15),
                'created_at' => Carbon::now()
            ]
        );
        //TODO переклади queue send
        Mail::raw("Твій код для входу в гру: $code", function ($message) use ($email) {
            $message->to($email)->subject('Код доступу');
        });

        return response()->json(['message' => 'Code has been sent to your email.']);
    }

    // 2. Перевірка коду та логін
    public function loginWithCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string'
        ]);

        $record = DB::table('otp_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Wrong code'], 401);
        }
        $color = '#' . substr(md5($request->email), 0, 6);
        // Знайти користувача або створити нового, якщо його ще немає
        $user = User::firstOrCreate(
            ['email' => $request->email],
            ['name' => explode('@', $request->email)[0], 'email_verified_at' => now(), 'color' => $color]
        );

        // Видалити код після використання
        DB::table('otp_codes')->where('email', $request->email)->delete();

        // Видати Sanctum токен
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }
}
