<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use App\Models\Permission;
use App\Http\Requests\CustomEmailVerificationRequest;
use Illuminate\Support\Str;
use Exception;

class AuthController extends Controller
{
  protected $userPermissions;
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 422);
    }

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    $user->sendEmailVerificationNotification();

    return response()->json(['message' => 'Please check your email to verify your account.'], 201);
  }

  public function authToken(ServerRequestInterface $serverRequest, AccessTokenController $accessTokenController)
  {
    return $accessTokenController->issueToken($serverRequest);
  }

  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|string|email',
      'password' => 'required|string',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
      return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = Auth::user();

    if (!$user->hasVerifiedEmail()) {
      return response()->json(['message' => 'Please verify your email first.'], 403);
    }

    return $this->issueToken($request);
  }

  public function issueToken($request)
  {
    Log::info("login: issueToken started");
    try {
      $oauthTokenUrl = url(env('APP_URL') . '/api/v1/oauth/token');
      $response = Http::withOptions([
        'verify' => false
      ])->asForm()->post($oauthTokenUrl, [
        'grant_type' => 'password',
        'client_id' => env('PASSWORD_CLIENT_ID'),
        'client_secret' => env('PASSWORD_CLIENT_SECRET'),
        'username' => $request->email,
        'password' => $request->password,
        'scope' => $request->scope ?? '',
      ]);

      if ($response->failed()) {
        return response()->json(['message' => 'Failed to obtain token.'], 500);
      }

      return $response->json();
    } catch (Exception $e) {
      return response()->json([
        'error' => [
          'message' => $e->getMessage()
        ]
      ], 403);
    }
  }

  public function forgotPassword(Request $request)
  {
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
      return response()->json(['message' => 'Email not found'], 400);
    }

    $status = Password::sendResetLink(
      $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
      ? response()->json(['message' => __($status)])
      : response()->json(['message' => __($status)], 500);
  }

  public function resetPassword(Request $request)
  {
    $request->validate([
      'token' => 'required',
      'email' => 'required|email',
      'password' => 'required|string|min:8|confirmed',
    ]);


    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function (User $user, string $password) {
        $user->forceFill([
          'password' => Hash::make($password),
          'remember_token' => Str::random(60)
        ])->save();
      }
    );

    return $status === Password::PASSWORD_RESET
      ? response()->json(['message' => __($status)])
      : response()->json(['message' => __($status)], 500);
  }

  public function verify(CustomEmailVerificationRequest $request)
  {
    if (! $request->hasValidSignature()) {
      return response()->json(['message' => 'Invalid or expired link.', 'status' => 'expired'], 400);
    }

    if ($request->user()->hasVerifiedEmail()) {
      return response()->json(['message' => 'Email already verified'], 200);
    }

    $request->fulfill();

    return response()->json(['message' => 'Email verified successfully'], 200);
  }

  public function resend(Request $request)
  {
    $request->validate([
      'id' => 'required|integer',
      'hash' => 'required|string',
    ]);

    $user = User::find($request->id);

    if (!$user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    if ($user->hasVerifiedEmail()) {
      return response()->json(['message' => 'Email already verified'], 200);
    }

    $user->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link sent!'], 200);
  }

  public function logout(Request $request)
  {
    $request->user()->token()->revoke();
    return response()->json([
      'message' => 'Successfully logged out',
      'access_token' => null,
      'refresh_token' => null,
    ]);
  }
  public function user(Request $request)
  {
    $user = $request->user();
    $permissions = $user->roles->flatMap(function ($role) {
      return $role->permissions;
    })->unique('name');

    return response()->json([
      'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
      ],
      'roles' => $user->roles->pluck('name')->toArray(),
      'permissions' => $permissions->pluck('name')->toArray(),
      'modules' => $this->modules($request),
    ], 200);
  }


  public function modules(Request $request)
  {
    $permissions = $request->user()->getAllPermissions()->pluck(['id']);

    $this->userPermissions = Permission::whereIn('id', $permissions)
      ->with(['module'])
      ->get()
      ->pluck(['module']);

    $menuLinks = $this->getParentLinks();

    $secondLevelLinks = $this->getChildrenLinks();

    $loopCounter = 0;
    while ($loopCounter <= count($menuLinks) - 1) {
      $menuIndex = $menuLinks[$loopCounter]['id'];
      if (isset($secondLevelLinks[$menuIndex])) {
        $menuLinks[$loopCounter]['items'] = $secondLevelLinks[$menuIndex];
      }
      $loopCounter++;
    }
    return $menuLinks;
  }

  protected function getParentLinks()
  {
    $parentLinks = $this->userPermissions->map(function ($item, $key) {
      if (isset($item->parent)) {
        $linkMeta = $item->parent->only(['id', 'weight', 'display_name', 'icon_class']);
        return [
          'id' => $linkMeta['id'],
          'weight' => $linkMeta['weight'],
          'title' => $linkMeta['display_name'],
          'action' => $linkMeta['icon_class'],
          'icon_class' => $linkMeta['icon_class'],
          'items' => []
        ];
      }

      return [
        'id' => $item['id'],
        'weight' => $item['weight'],
        'display_name' => $item['display_name'],
        'icon_class' => $item['icon_class'],
        'route' => $item['route']
      ];
    });

    return $parentLinks->unique()->sortBy('weight')->values()->all();
  }

  /**
   * Build second level menu items
   *
   * @return array
   */
  protected function getChildrenLinks()
  {
    $navigationLinks = $this->userPermissions->map(function ($item, $key) {
      return $item->only(['weight', 'parent_id', 'display_name', 'icon_class', 'route']);
    });

    return $navigationLinks
      ->unique()
      ->sortBy('weight')
      ->values()
      ->groupBy('parent_id')
      ->toArray();
  }
}
