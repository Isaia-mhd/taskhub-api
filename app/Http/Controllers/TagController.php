<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Services\TagService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    use JsonResponseTrait;

    public function __construct(private TagService $tagService) {}

    public function getByWorkspace(string $workspace): JsonResponse
    {
        $tags = $this->tagService->getByWorkspace($workspace);

        return $this->successJson(
            'Tags retrieved successfully.',
            TagResource::collection($tags->load('workspace'))
        );
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = $this->tagService->store($request->validated());

        return $this->successJson(
            'Tag stored successfully.',
            new TagResource($tag->load('workspace')),
            201
        );
    }

    public function show(int $tag): JsonResponse
    {
        $tag = $this->tagService->getById($tag);

        return $this->successJson(
            'Tag retrieved successfully.',
            new TagResource($tag->load('workspace'))
        );
    }

    public function update(UpdateTagRequest $request, int $tag): JsonResponse
    {
        $tag = $this->tagService->update($tag, $request->validated());

        return $this->successJson(
            'Tag updated successfully.',
            new TagResource($tag->load('workspace'))
        );
    }

    public function destroy(int $tag): JsonResponse
    {
        $this->tagService->destroy($tag);

        return $this->successJson('Tag deleted successfully.');
    }
}
