<?php

namespace App\Domain\UseCase\User;

use App\Domain\Objects\User\User;
use App\Domain\UseCase\Authentication\JwtManager;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DateTime;
use Webmozart\Assert\Assert;

class CreateUser
{
    private array $userData;

    public function __construct(array $userData)
    {
        $this->userData = $userData;
        $this->validateData();
    }

    public function execute(): array
    {
        $now = new DateTime();

        //username should be unique!
        $user = new User(
            null,
            $this->userData['username'],
            password_hash($this->userData['password'], PASSWORD_BCRYPT),
            $this->userData['first_name'] ?? '',
            $this->userData['last_name'] ?? '',
            $this->userData['email'],
            $this->userData['photo'] ?? '',
            $now,
            $now,
            $now
        );
        $user = (new InMemoryUserRepository())->insert($user);
        $token = JwtManager::encode(JwtManager::getPayload($user->getId(), $user->getUsername()));
        return [$user, $token];
    }

    private function validateData(): void
    {
        /** Username
         * Must start with letter
         * 6-32 characters
         * Letters and numbers only
         */
        Assert::keyExists($this->userData, 'username', 'Username is mandatory.');
        Assert::regex(
            $this->userData['username'],
            '/^[A-Za-z][A-Za-z0-9]{5,31}$/',
            "Username Must: \n start with letter \n 6-32 characters \n Letters and numbers only"
        );

        /** Password
         * Must have more than 8 characters
         * Has at least one lowercase, uppercase letter and number, and symbol
         */
        Assert::keyExists($this->userData, 'password', 'Password is mandatory.');
        Assert::regex(
            $this->userData['password'],
            '/^(?=\P{Ll}*\p{Ll})(?=\P{Lu}*\p{Lu})(?=\P{N}*\p{N})(?=[\p{L}\p{N}]*[^\p{L}\p{N}])[\s\S]{8,}$/',
            "Password Must: \n have at least 8 characters \n Has at least one lowercase, uppercase letter and number, and symbol"
        );

        Assert::keyExists($this->userData, 'email', 'Email is mandatory.');
        Assert::email($this->userData['email'], 'Email is not valid.');
    }
}
