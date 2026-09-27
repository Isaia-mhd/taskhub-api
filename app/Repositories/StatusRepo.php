<?php

namespace App\Repositories;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Status;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class StatusRepo
{
    public function getById(int $statusId): ?Status
    {
        return Status::find($statusId);
    }

    public function update(Status $status, array $data): Status
    {
        $status->update($data);

        return $status;
    }

    public function destroy(Status $status): bool
    {
        return $status->delete();
    }

    public function store(Workspace $workspace, array $data): Status
    {
        return $workspace->statuses()->create($data);
    }

    public function reorder(Workspace $workspace, array $data): void
    {
        DB::transaction(function () use ($workspace, $data) {
            foreach ($data['statuses'] as $item) {
                $status = Status::where('workspace_id', $workspace->id)
                    ->where('id', $item['id'])
                    ->first();

                if (! $status) {
                    throw new ResourceNotFoundException('Status not found.');
                }

                $status->update([
                    'position' => $item['position'],
                ]);
            }
        });
    }
}
