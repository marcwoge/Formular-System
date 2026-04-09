<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FormController extends Controller
{
    public function index(): JsonResponse
    {
        $forms = Form::query()
            ->with(['currentVersion:id,form_id,version_number,status,published_at', 'creator:id,name,email'])
            ->orderBy('title')
            ->get();

        return response()->json(['data' => $forms]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $this->validateFormPayload($request, true);
        $user = $request->user();

        $form = DB::transaction(function () use ($payload, $user) {
            $form = Form::query()->create([
                'slug' => $payload['slug'],
                'title' => $payload['title'],
                'description' => $payload['description'] ?? null,
                'category' => $payload['category'] ?? null,
                'status' => 'draft',
                'visibility_scope' => $payload['visibility_scope'] ?? 'internal',
                'sensitivity' => $payload['sensitivity'] ?? 'normal',
                'allowed_contexts' => $payload['allowed_contexts'] ?? ['internal'],
                'allow_user_edits' => $payload['allow_user_edits'] ?? false,
                'allow_corrections' => $payload['allow_corrections'] ?? true,
                'workflow_definition' => $payload['workflow_definition'] ?? null,
                'retention_definition' => $payload['retention_definition'] ?? null,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            $version = FormVersion::query()->create([
                'form_id' => $form->id,
                'version_number' => 1,
                'title' => $payload['title'],
                'description' => $payload['description'] ?? null,
                'status' => 'draft',
                'definition' => $payload['definition'],
                'change_summary' => $payload['change_summary'] ?? 'Initial version',
                'created_by' => $user->id,
            ]);

            $form->forceFill([
                'current_version_id' => $version->id,
            ])->save();

            return $form->fresh(['versions', 'currentVersion']);
        });

        return response()->json(['data' => $form], JsonResponse::HTTP_CREATED);
    }

    public function show(Form $form): JsonResponse
    {
        $form->load([
            'versions.creator:id,name,email',
            'versions.publisher:id,name,email',
            'currentVersion',
            'creator:id,name,email',
            'updater:id,name,email',
        ]);

        return response()->json(['data' => $form]);
    }

    public function update(Request $request, Form $form): JsonResponse
    {
        $payload = $this->validateFormPayload($request, false, $form->id);

        $form->fill([
            'slug' => $payload['slug'] ?? $form->slug,
            'title' => $payload['title'] ?? $form->title,
            'description' => $payload['description'] ?? $form->description,
            'category' => $payload['category'] ?? $form->category,
            'visibility_scope' => $payload['visibility_scope'] ?? $form->visibility_scope,
            'sensitivity' => $payload['sensitivity'] ?? $form->sensitivity,
            'allowed_contexts' => $payload['allowed_contexts'] ?? $form->allowed_contexts,
            'allow_user_edits' => $payload['allow_user_edits'] ?? $form->allow_user_edits,
            'allow_corrections' => $payload['allow_corrections'] ?? $form->allow_corrections,
            'workflow_definition' => $payload['workflow_definition'] ?? $form->workflow_definition,
            'retention_definition' => $payload['retention_definition'] ?? $form->retention_definition,
            'updated_by' => $request->user()->id,
        ])->save();

        return response()->json(['data' => $form->fresh(['currentVersion'])]);
    }

    public function addVersion(Request $request, Form $form): JsonResponse
    {
        $payload = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'definition' => ['required', 'array'],
            'change_summary' => ['nullable', 'string', 'max:1000'],
        ]);

        $nextVersion = (int) $form->versions()->max('version_number') + 1;

        $version = FormVersion::query()->create([
            'form_id' => $form->id,
            'version_number' => $nextVersion,
            'title' => $payload['title'] ?? $form->title,
            'description' => $payload['description'] ?? $form->description,
            'status' => 'draft',
            'definition' => $payload['definition'],
            'change_summary' => $payload['change_summary'] ?? 'Version update',
            'created_by' => $request->user()->id,
        ]);

        $form->forceFill([
            'current_version_id' => $version->id,
            'status' => 'draft',
            'updated_by' => $request->user()->id,
        ])->save();

        return response()->json(['data' => $version], JsonResponse::HTTP_CREATED);
    }

    public function publish(Request $request, Form $form, FormVersion $version): JsonResponse
    {
        abort_unless($version->form_id === $form->id, JsonResponse::HTTP_NOT_FOUND);

        DB::transaction(function () use ($request, $form, $version) {
            $form->versions()->where('status', 'published')->update(['status' => 'superseded']);

            $version->forceFill([
                'status' => 'published',
                'published_by' => $request->user()->id,
                'published_at' => now(),
            ])->save();

            $form->forceFill([
                'title' => $version->title,
                'description' => $version->description,
                'status' => 'active',
                'current_version_id' => $version->id,
                'updated_by' => $request->user()->id,
                'published_at' => now(),
            ])->save();
        });

        return response()->json([
            'data' => $form->fresh(['currentVersion', 'versions']),
        ]);
    }

    protected function validateFormPayload(Request $request, bool $isCreate, ?int $formId = null): array
    {
        $slugRules = ['string', 'max:160', Rule::unique('forms', 'slug')];
        if (! $isCreate) {
            $slugRules = ['sometimes', 'string', 'max:160', Rule::unique('forms', 'slug')->ignore($formId)];
        }

        return $request->validate([
            'slug' => array_merge($isCreate ? ['required'] : [], $slugRules),
            'title' => [$isCreate ? 'required' : 'sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:120'],
            'visibility_scope' => ['nullable', Rule::in(['internal', 'external', 'guest', 'kiosk'])],
            'sensitivity' => ['nullable', Rule::in(['normal', 'sensitive', 'restricted'])],
            'allowed_contexts' => ['nullable', 'array'],
            'allowed_contexts.*' => ['string', Rule::in(['internal', 'external', 'guest', 'kiosk'])],
            'allow_user_edits' => ['nullable', 'boolean'],
            'allow_corrections' => ['nullable', 'boolean'],
            'workflow_definition' => ['nullable', 'array'],
            'retention_definition' => ['nullable', 'array'],
            'definition' => [$isCreate ? 'required' : 'nullable', 'array'],
            'change_summary' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}