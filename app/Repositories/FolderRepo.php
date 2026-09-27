<?php

namespace App\Repositories;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Folder;
use App\Models\Space;
use Illuminate\Support\Facades\DB;

class FolderRepo
{
    public function getById(string $folderId): ?Folder
    {
        return Folder::find($folderId);
    }

    public function update(Folder $folder, array $data): Folder
    {
        $folder->update($data);

        return $folder;
    }

    public function destroy(Folder $folder): bool
    {
        return $folder->delete();
    }

    public function store(Space $space, array $data): Folder
    {
        return $space->folders()->create($data);
    }

    public function reorder(Space $space, array $data): void
    {
        DB::transaction(function () use ($space, $data) {
            foreach ($data['folders'] as $item) {
                $folder = Folder::where('space_id', $space->id)
                    ->where('id', $item['id'])
                    ->first();

                if (! $folder) {
                    throw new ResourceNotFoundException('Folder not found.');
                }

                $folder->update([
                    'position' => $item['position'],
                ]);
            }
        });
    }
}
