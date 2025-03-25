<?php

namespace App\Domain\UseCase\User;

use App\Domain\DomainException\RegisterError;
use App\Domain\Objects\User\User;
use App\Domain\Objects\User\UserNotFoundException;
use App\Domain\Objects\User\UserRepository;
use App\Domain\UseCase\Authentication\JwtManager;
use App\Domain\UseCase\UseCase;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use Exception;
use Webmozart\Assert\Assert;
use DateTime;

class CreateUser extends UseCase
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

        /** @var User $user */
        $user = $this->userRepository->insert($user);
        $token = JwtManager::encode(JwtManager::getPayload($user->getId(), $user->getUsername()));
        return [$user, $token];
    }

    protected function validateData(): void
    {
        Assert::keyExists($this->userData, 'username', 'Username is mandatory.');
        Assert::keyExists($this->userData, 'password', 'Password is mandatory.');
        Assert::keyExists($this->userData, 'email', 'Email is mandatory.');

        try {
            /** Username
             * Must start with letter
             * 6-32 characters
             * Letters and numbers only
             */
            Assert::regex(
                $this->userData['username'],
                '/^[A-Za-z][A-Za-z0-9]{5,31}$/',
                "Username Must: \n start with letter \n 6-32 characters \n Letters and numbers only"
            );

            Assert::null($this->isUsernameTaken(), 'Username is already taken.');
            /** Password
             * Must have more than 8 characters
             * Has at least one lowercase, uppercase letter and number, and symbol
             */
            Assert::regex(
                $this->userData['password'],
                '/^(?=\P{Ll}*\p{Ll})(?=\P{Lu}*\p{Lu})(?=\P{N}*\p{N})(?=[\p{L}\p{N}]*[^\p{L}\p{N}])[\s\S]{8,}$/',
                "Password Must: \n have at least 8 characters \n Has at least one lowercase, uppercase letter and number, and symbol"
            );

            Assert::email($this->userData['email'], 'Email is not valid.');
        } catch (Exception $e) {
            throw new RegisterError($e->getMessage());
        }
    }

    public function isUsernameTaken(): ?User
    {
        try {
            $isUsernameTaken = $this->userRepository->findUserOfUsername($this->userData['username']);
        } catch (UserNotFoundException $e) {
            $isUsernameTaken = null;
        }
        return $isUsernameTaken;
    }
}
