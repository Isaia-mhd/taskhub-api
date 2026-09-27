<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderFolderRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;
use App\Http\Resources\FolderResource;
use App\Services\FolderService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

class FolderController extends Controller
{
    use JsonResponseTrait;

    public function __construct(private FolderService $folderService) {}

    public function getBySpace(string $space): JsonResponse
    {
        $folders = $this->folderService->getBySpace($space);

        return $this->successJson(
            'Folders retrieved successfully.',
            FolderResource::collection($folders->load('space'))
        );
    }

    public function store(StoreFolderRequest $request): JsonResponse
    {
        $folder = $this->folderService->store($request->validated());

        return $this->successJson(
            'Folder stored successfully.',
            new FolderResource($folder->load('space')),
            201
        );
    }

    public function show(string $folder): JsonResponse
    {
        $folder = $this->folderService->getById($folder);

        return $this->successJson(
            'Folder retrieved successfully.',
            new FolderResource($folder->load('space'))
        );
    }

    public function update(UpdateFolderRequest $request, string $folder): JsonResponse
    {
        $folder = $this->folderService->update($folder, $request->validated());

        return $this->successJson(
            'Folder updated successfully.',
            new FolderResource($folder->load('space'))
        );
    }

    public function destroy(string $folder): JsonResponse
    {
        $this->folderService->destroy($folder);

        return $this->successJson('Folder deleted successfully.');
    }

    public function reorder(ReorderFolderRequest $request, string $space): JsonResponse
    {
        $this->folderService->reorder($space, $request->validated());

        return $this->successJson('Folder reordered successfully.');
    }
}
