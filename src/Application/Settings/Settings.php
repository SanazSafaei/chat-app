<?php

declare(strict_types=1);

namespace App\Application\Settings;

use Slim\Logger;
use Webmozart\Assert\Assert;

class Settings implements SettingsInterface
{
    private array $settings;

    public function __construct(array $settings)
    {
        $config = self::loadConfig();
        $settings = array_merge($settings, $config);
        $this->settings = $settings;
    }

    /**
     * @return mixed
     */
    public function get(string $key = '')
    {
        return (empty($key)) ? $this->settings : $this->settings[$key];
    }

    private static function loadConfig(?string $mode = null)
    {

        $configDir = __DIR__ . '/../config';
        $dirs[] = $configDir;

        if ($mode) {
            $dirs[] = $configDir . '/' . $mode;
        }
        $config = [];

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $scanned_directory = array_diff(scandir($dir), ['..', '.']);
                foreach ($scanned_directory as $file) {
                    $yml = file_get_contents($dir . '/' . $file);
                    $ymlContent = self::extractYml($yml);
                    Assert::notNull($yml, 'Please check ' . $file);
                    $key = explode('.', $file)[0];
                    $config[$key] = $ymlContent;
                }
            }
        }
        return $config;
    }

    public static function extractYml(false|string $yml): array
    {
        $lines = explode("\n", $yml);
        $ymlContent = [];
        foreach ($lines as $line) {
            $words = explode(':', $line);
            $ymlContent[$words[0]] = $words[1];
        }
        return $ymlContent;
    }
}
