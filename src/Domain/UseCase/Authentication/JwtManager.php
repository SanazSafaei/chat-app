<?php

namespace App\Domain\UseCase\Authentication;

use App\Application\Settings\Settings;
use DateTimeImmutable;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use InvalidArgumentException;
use stdClass;

class JwtManager
{
    private const string HS256_ALGO = 'HS256';

    private static function getPrivateKey(): string
    {
        try {
            $jwtConfig = (new Settings([]))->get('auth-secret');
        } catch (InvalidArgumentException $e) {
            $jwtConfig = [
                'secret_key' => 'temp-key'
            ];
        }
        return $jwtConfig['secret_key'];
    }

    private static function getTtl(): int
    {
        try {
            $jwtConfig = (new Settings([]))->get('auth-secret');
        } catch (InvalidArgumentException $e) {
                $jwtConfig = [
                    'ttl' => 3600
                ];
        }
        return $jwtConfig['ttl'];
    }

    public static function decode(string $jwt): stdClass
    {
        $key = self::getPrivateKey();
        $payload = JWT::decode($jwt, new Key($key, self::HS256_ALGO));
        $now = new DateTimeImmutable();
        if ($payload->expires < $now->getTimestamp()) {
            throw new Exception('Token expired');
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
