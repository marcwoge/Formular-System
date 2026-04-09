<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class AccessCatalogController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'roles' => Role::query()->orderBy('name')->get(['id', 'name', 'slug', 'description', 'is_system']),
            'permissions' => Permission::query()->orderBy('scope')->orderBy('name')->get(['id', 'name', 'slug', 'scope', 'description']),
            'groups' => Group::query()->orderBy('name')->get(['id', 'name', 'slug', 'source', 'is_active']),
        ]);
    }
}