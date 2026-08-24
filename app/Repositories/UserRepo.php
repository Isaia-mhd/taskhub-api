<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepo
{
    public function store(array $user): User
    {
        return User::create($user);
    }

    public function updateAvatar(User $user, string $avatarPath): User
    {
        $user->update(['avatar' => $avatarPath]);

        return $user;
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    public function deleteAccount(User $user): bool
    {
        return $user->delete();
    }
}
