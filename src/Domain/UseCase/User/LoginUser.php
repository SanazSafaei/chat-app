<?php

namespace App\Domain\UseCase\User;

use App\Domain\UseCase\Authentication\JwtManager;
use App\Domain\UseCase\UseCase;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use Webmozart\Assert\Assert;

class LoginUser extends UseCase
{
    private array $userData;

    public function __construct(array $userData)
    {
        $this->userData = $userData;
        parent::__construct();
    }

    public function execute(): array
    {
        $user = (new InMemoryUserRepository())->findUserOfUsername($this->userData['username']);
        $isVerified = password_verify($this->userData['password'], $user->getPassword());
        if (!$isVerified) {
            throw new \Exception('Invalid credentials.');
        }
        (new InMemoryUserRepository())->updateField('last_seen', (new \DateTime())->format('Y-m-d H:i:s'), $user->getId());
        $token = JwtManager::encode(JwtManager::getPayload($user->getId(), $user->getUsername()));
        return [$user, $token];
    }

    protected function validateData(): void
    {
        Assert::keyExists($this->userData, 'username', 'Username is mandatory.');
        Assert::keyExists($this->userData, 'password', 'Password is mandatory.');
    }
}
