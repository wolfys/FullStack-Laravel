<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'registration','getActivateEmail']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Не верный email или пароль'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * User registration
     */
    public function registration(Request $request): JsonResponse
    {
        // Проверяем что пришло все верно и не было таких пользователей в нашей базе данных.
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        // Получаем почту из $request мы будем использовать её часто.
        $email = request('email');
        $name = request('name');
        $token = generateRandomCode(2,100);

        // Заполняем Модельку и отправляем её в базу данных.
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->is_admin = 0;
        $user->remember_token = $token;
        $user->password = Hash::make(request('password'));
        $user->save();

        // Оформляем полученный id пользователя в переменную для дальнейшего использования
        $userId = $user->id;

        // Теперь генерируем код, который будет в Email пользователя

        // Отправляем почтовое письмо.
        Mail::to($email)->send(new WelcomeMail($userId, $token, $name));

        return response()->json(
            [
                'message' => 'Успешная регистрация!'
            ]
        );
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Успешный выход с сайта. До скорой встречи!']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        if(!auth()->user()->email_verified_at) {
            auth()->logout();
            return response()->json(['error' => 'Ваша электронная почта не активирована'], 401);
        }
        // Обновляем дату когда зашёл пользователь
        $user = User::find(auth()->user()->id);
        $user->updated_at = now();
        $user->save();

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'tokenTime' => Carbon::now()->addSeconds(60 * 60 - 10)->toDateTimeString(),
            'me' => response()->json(auth()->user())->original,
        ]);
    }

    public function getActivateEmail(Request $request) {
        $data = User::where(['id' => $request->get('id'), 'remember_token' => $request->get('token')])->first();
        if(empty($data)) {
            return response()->json(
                [
                    'message' => 'Неверная ссылка для активации почты.'
                ]
            );
        }
        if($data->email_verified_at) {
            return response()->json(
                [
                    'message' => 'Ваша почта уже активирована'
                ]
            );
        }
        $user = User::find($request->get('id'));
        $user->email_verified_at = Carbon::now()->timestamp;
        $user->save();

        return response()->json(
            [
                'message' => 'Успешная активация электронной почты'
            ]
        );

    }
}
