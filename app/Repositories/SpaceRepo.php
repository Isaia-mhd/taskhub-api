<?php

namespace App\Repositories;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Space;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SpaceRepo
{

    public function getById(string $spaceId): ?Space
    {
        return Space::find($spaceId);
    }

    public function update(Space $space, array $data): Space
    {
        $space->update($data);
        return $space;
    }

    public function destroy(Space $space): bool
    {
        return $space->delete();
    }

    public function store(Workspace $workspace, array $data): Space
    {
        return $workspace->spaces()->create($data);
    }

    public function reorder(Workspace $workspace, array $data): void
    {

        DB::transaction(function() use ($workspace, $data){

            foreach ($data["spaces"] as $item) {

                $space = Space::where('workspace_id', $workspace->id)->where('id', $item['id'])->first();

                if (!$space) throw new ResourceNotFoundException('Space not found.');
                
                $space->update([
                    'position' => $item['position']
                ]);
            }
            
        });
    }
}
