<?php

namespace App\Domain\UseCase\Authentication;

use App\Application\Settings\Settings;
use DateTimeImmutable;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class JwtManager
{
    const string HS256_ALGO = 'HS256';

    private static function getPrivateKey(): string
    {
        $jwtConfig = (new Settings([]))->get('auth-secret');
        return $jwtConfig['secret_key'];
    }

    private static function getTtl(): int
    {
        $jwtConfig = (new Settings([]))->get('auth-secret');
        return $jwtConfig['ttl'];
    }

    public static function decode(string $jwt): stdClass
    {
        $key = self::getPrivateKey();
        $payload = JWT::decode($jwt, new Key($key, self::HS256_ALGO));
        $now = new DateTimeImmutable();
        if($payload->expires < $now->getTimestamp()) {
            throw new \Exception('Token expired');
        }
        return $payload;
    }

    public static function encode(array $payload): string
    {
        $key = self::getPrivateKey();
        return JWT::encode($payload, $key, self::HS256_ALGO);
    }

    public static function getPayload(int $userId, string $username): array
    {
        return [
            'userId' => $userId,
            'username' => $username,
            'host' => $_SERVER['HTTP_HOST'] ?? '',
            'expires' => time() + self::getTtl()
        ];
    }

}