<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Interfaces\FileStorageInterface;
use App\Models\User;
use App\Repositories\UserRepo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepo $userRepo,
        private FileStorageInterface $storage
    ){}

    public function store(array $user): User
    {
        $user['password'] = Hash::make($user['password']);

        $user['role'] = UserRole::OWNER->value;
        
        return $this->userRepo->store($user);
    }

    public function updateAvatar(User $user, UploadedFile $avatar): User
    {
        $oldAvatar = $user->avatar;
        
        // Store the new one
        $path = $this->storage->upload($avatar, 'avatars');
        
        // Save to database
        $user = $this->userRepo->updateAvatar($user, $path);
        
        // delete the old avatar if exists
        if($oldAvatar) $this->storage->delete($oldAvatar);

        return $user;
    }

    public function update(User $user, array $data): User
    {
        if(isset($data['password']))
        {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepo->update($user, $data);
    }

    public function deleteAccount(User $user): bool
    {
        return $this->userRepo->deleteAccount($user);
    }
}
