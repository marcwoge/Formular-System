<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        /** @var User|null $user */
        $user = User::query()
            ->with(['roles.permissions', 'groups.roles.permissions'])
            ->where('email', $credentials['email'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Die angegebenen Zugangsdaten sind ungueltig.'],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Der Benutzer ist nicht aktiv.',
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        $tokenName = $credentials['device_name'] ?? 'FormsHub API';
        $abilities = array_values(array_unique(array_merge(['*'], $user->permissionSlugs())));
        $token = $user->createToken($tokenName, $abilities);

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token->plainTextToken,
            'user' => $this->serializeUser($user->fresh(['roles.permissions', 'groups.roles.permissions'])),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Abgemeldet.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user()->load(['roles.permissions', 'groups.roles.permissions']);

        return response()->json([
            'user' => $this->serializeUser($user),
        ]);
    }

    protected function serializeUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'display_name' => $user->display_name,
            'login_name' => $user->login_name,
            'email' => $user->email,
            'status' => $user->status,
            'department' => $user->department,
            'location' => $user->location,
            'is_guest' => $user->is_guest,
            'roles' => $user->roles->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
            ])->values(),
            'groups' => $user->groups->map(fn ($group) => [
                'id' => $group->id,
                'name' => $group->name,
                'slug' => $group->slug,
            ])->values(),
            'permissions' => $user->allPermissions()->map(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'slug' => $permission->slug,
                'scope' => $permission->scope,
            ])->values(),
        ];
    }
}