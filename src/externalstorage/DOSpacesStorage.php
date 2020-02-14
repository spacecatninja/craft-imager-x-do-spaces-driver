<?php
/**
 * Digital Ocean Spaces external storage driver for Imager X
 *
 * @link      https://www.spacecat.ninja/
 * @copyright Copyright (c) 2020 André Elvan
 */

namespace spacecatninja\imagerxdospacesdriver\externalstorage;

use Craft;
use craft\helpers\FileHelper;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

use spacecatninja\imagerx\models\ConfigModel;
use spacecatninja\imagerx\services\ImagerService;
use spacecatninja\imagerx\externalstorage\ImagerStorageInterface;

class DOSpacesStorage implements ImagerStorageInterface
{
    /**
     * @param string $file
     * @param string $uri
     * @param bool $isFinal
     * @param array $settings
     * @return bool
     */
    public static function upload(string $file, string $uri, bool $isFinal, array $settings)
    {
        /** @var ConfigModel $settings */
        $config = ImagerService::getConfig();

        $clientConfig = [
            'version' => 'latest',
            'endpoint' => Craft::parseEnv($settings['endpoint']),
            'region' => Craft::parseEnv($settings['region']),
            'credentials' => [
                'key' => Craft::parseEnv($settings['accessKey']),
                'secret' => Craft::parseEnv($settings['secretAccessKey']),
            ],
        ];

        try {
            $s3 = new S3Client($clientConfig);
        } catch (\InvalidArgumentException $e) {
            Craft::error('Invalid configuration of S3 Client: '.$e->getMessage(), __METHOD__);
            return false;
        }
        
        if (isset($settings['folder']) && $settings['folder'] !== '') {
            $uri = ltrim(FileHelper::normalizePath(Craft::parseEnv($settings['folder']).'/'.$uri), '/');
        }
        
        // Always use forward slashes for S3
        $uri = str_replace('\\', '/', $uri);

        $opts = $settings['requestHeaders'];
        $cacheDuration = $isFinal ? $config->cacheDurationExternalStorage : $config->cacheDurationNonOptimized;

        if (!isset($opts['Cache-Control'])) {
            $opts['CacheControl'] = 'max-age='.$cacheDuration.', must-revalidate';
        }

        $opts = array_merge($opts, [
            'Bucket' => Craft::parseEnv($settings['bucket']),
            'Key' => $uri,
            'Body' => fopen($file, 'r'),
            'ACL' => 'public-read',
        ]);

        try {
            $s3->putObject($opts);
        } catch (S3Exception $e) {
            Craft::error('An error occured while uploading to Digital Ocean Spaces: '.$e->getMessage(), __METHOD__);
            return false;
        }

        return true;
    }
}
