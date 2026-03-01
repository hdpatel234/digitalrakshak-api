<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserService extends BaseService
{
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }

    // column costants
    public function clientID()
    {
        return $this->repository->clientID();
    }
    public function userType()
    {
        return $this->repository->userType();
    }
    public function firstName()
    {
        return $this->repository->firstName();
    }
    public function lastName()
    {
        return $this->repository->lastName();
    }
    public function email()
    {
        return $this->repository->email();
    }
    public function emailVerifiedAt()
    {
        return $this->repository->emailVerifiedAt();
    }
    public function phoneCode()
    {
        return $this->repository->phoneCode();
    }
    public function phone()
    {
        return $this->repository->phone();
    }
    public function password()
    {
        return $this->repository->password();
    }
    public function rememberToken()
    {
        return $this->repository->rememberToken();
    }
    public function avatar()
    {
        return $this->repository->avatar();
    }
    public function lastLoginAt()
    {
        return $this->repository->lastLoginAt();
    }
    public function lastLoginIp()
    {
        return $this->repository->lastLoginIp();
    }
    public function lastLoginBrowser()
    {
        return $this->repository->lastLoginBrowser();
    }
    public function lastLoginDevice()
    {
        return $this->repository->lastLoginDevice();
    }
    public function lastLoginOs()
    {
        return $this->repository->lastLoginOs();
    }
    public function lastLoginProvider()
    {
        return $this->repository->lastLoginProvider();
    }

    public function lastLoginProviderId()
    {
        return $this->repository->lastLoginProviderId();
    }
    public function isActive()
    {
        return $this->repository->isActive();
    }
    public function isAdmin()
    {
        return $this->repository->isAdmin();
    }

    // functions
    public function getByEmail($email)
    {
        return $this->repository->getByEmail($email);
    }

    public function updateProfile(User $user, array $data, ?UploadedFile $avatar = null): User
    {
        $payload = [];

        $fieldMap = [
            'first_name' => $this->firstName(),
            'last_name' => $this->lastName(),
            'email' => $this->email(),
            'phone_code' => $this->phoneCode(),
            'phone' => $this->phone(),
        ];

        foreach ($fieldMap as $requestKey => $column) {
            if (array_key_exists($requestKey, $data)) {
                $payload[$column] = $data[$requestKey];
            }
        }

        $shouldRemoveAvatar = (bool) ($data['remove_logo'] ?? false);
        $currentAvatar = $user->{$this->avatar()};

        if ($avatar) {
            if ($currentAvatar) {
                Storage::disk('public')->delete($currentAvatar);
            }

            try {
                // Check if directory exists and is writable
                $directory = storage_path('app/public/users/avatar');
                Log::info('Avatar directory:', [
                    'path' => $directory,
                    'exists' => file_exists($directory),
                    'is_writable' => is_writable($directory),
                    'owner' => function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($directory)) : 'unknown',
                    'permissions' => substr(sprintf('%o', fileperms($directory)), -4)
                ]);

                $path = $avatar->store('users/avatar', 'public');
                Log::info('Avatar stored successfully at: ' . $path);

                $payload[$this->avatar()] = $path;
            } catch (\Exception $e) {
                Log::error('Avatar upload failed: ' . $e->getMessage());
                throw $e;
            }
        } elseif ($shouldRemoveAvatar) {
            if ($currentAvatar) {
                Storage::disk('public')->delete($currentAvatar);
            }

            $payload[$this->avatar()] = null;
        }

        if ($payload !== []) {
            $user = $this->update($user->{$this->id()}, $payload);
        }

        return $user->fresh();
    }
}
