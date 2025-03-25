<?php

namespace App\Domain\UseCase\User;

use App\Domain\DomainException\InvalidCredentials;
use App\Domain\Objects\User\UserRepository;
use App\Domain\UseCase\Authentication\JwtManager;
use App\Domain\UseCase\UseCase;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use Webmozart\Assert\Assert;

class LoginUser extends UseCase
{
    private array $userData;
    private UserRepository $userRepository;

    public function __construct(array $userData, UserRepository $userRepository)
    {
        $this->userData = $userData;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    public function execute(): array
    {
        $user = $this->userRepository->findUserOfUsername($this->userData['username']);
        $isVerified = password_verify($this->userData['password'], $user->getPassword());
        if (!$isVerified) {
            throw new InvalidCredentials('Invalid credentials.');
        }
        $this->userRepository->updateField('last_seen', (new \DateTime())->format('Y-m-d H:i:s'), $user->getId());
        $token = JwtManager::encode(JwtManager::getPayload($user->getId(), $user->getUsername()));
        return [$user, $token];
    }

    protected function validateData(): void
    {
        Assert::keyExists($this->userData, 'username', 'Username is mandatory.');
        Assert::keyExists($this->userData, 'password', 'Password is mandatory.');
    }
}
