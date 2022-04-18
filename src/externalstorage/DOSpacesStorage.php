<?php
/**
 * Digital Ocean Spaces external storage driver for Imager X
 *
 * @link      https://www.spacecat.ninja/
 * @copyright Copyright (c) 2022 André Elvan
 */

namespace spacecatninja\imagerxdospacesdriver\externalstorage;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

use Craft;

use craft\helpers\App;
use craft\helpers\FileHelper;

use spacecatninja\imagerx\externalstorage\ImagerStorageInterface;
use spacecatninja\imagerx\models\ConfigModel;
use spacecatninja\imagerx\services\ImagerService;

class DOSpacesStorage implements ImagerStorageInterface
{
    /**
     * @param string $file
     * @param string $uri
     * @param bool   $isFinal
     * @param array  $settings
     *
     * @return bool
     */
    public static function upload(string $file, string $uri, bool $isFinal, array $settings): bool
    {
        /** @var ConfigModel $settings */
        $config = ImagerService::getConfig();

        $clientConfig = [
            'version' => 'latest',
            'endpoint' => App::parseEnv($settings['endpoint']),
            'region' => App::parseEnv($settings['region']),
            'credentials' => [
                'key' => App::parseEnv($settings['accessKey']),
                'secret' => App::parseEnv($settings['secretAccessKey']),
            ],
        ];

        try {
            $s3 = new S3Client($clientConfig);
        } catch (\InvalidArgumentException $e) {
            Craft::error('Invalid configuration of S3 Client: ' . $e->getMessage(), __METHOD__);

            return false;
        }

        if (isset($settings['folder']) && $settings['folder'] !== '') {
            $uri = ltrim(FileHelper::normalizePath(App::parseEnv($settings['folder']) . '/' . $uri), '/');
        }

        // Always use forward slashes
        $uri = str_replace('\\', '/', $uri);

        $opts = $settings['requestHeaders'];
        $cacheDuration = $isFinal ? $config->cacheDurationExternalStorage : $config->cacheDurationNonOptimized;

        if (!isset($opts['Cache-Control'])) {
            $opts['CacheControl'] = 'max-age=' . $cacheDuration . ', must-revalidate';
        }

        $opts = array_merge($opts, [
            'Bucket' => App::parseEnv($settings['bucket']),
            'Key' => $uri,
            'Body' => fopen($file, 'r'),
            'ACL' => 'public-read',
        ]);

        try {
            $s3->putObject($opts);
        } catch (S3Exception $e) {
            Craft::error('An error occured while uploading to Digital Ocean Spaces: ' . $e->getMessage(), __METHOD__);

            return false;
        }

        return true;
    }
}
