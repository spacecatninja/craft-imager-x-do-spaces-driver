# Imager X Storage Driver for DigitalOcean Spaces

This is an external storage driver for Imager X that uploads your Imager transforms to DigitalOcean's Spaces object storage. Spaces is an AWS S3 compatible object storage, so the plugin utilizes the same S3 client as Imager's AWS storage does.

This plugin also serves as a good reference point if you want to create your own external storage driver for Imager to integrate with an unsupported third-party object storage. It's really simple, and you can do it either from a Craft 3 plugin, if you want to share it with the rest of the community (please do!), or a module, if you're using something proprietary/custom.

## Requirements

This plugin requires Craft CMS 3.3.0 or later, and Imager X 3.0 or later. External storages are only available in the Pro edition of Imager. 

## Installation

To install the plugin, follow these instructions:

1. Install with composer via `composer require spacecatninja/imager-x-do-spaces-driver` from your project directory.
2. Install the plugin in the Craft Control Panel under Settings > Plugins, or from the command line via `./craft install/plugin imager-x-do-spaces-driver`.

## Configuration

Configure the storage driver by adding new key named `dospaces` to the `storagesConfig` config setting in your **imager-x.php config file**, with the following configuration:

    'storageConfig' => [
        'dospaces' => [
            'endpoint' => '',
            'accessKey' => '',
            'secretAccessKey' => '',
            'region' => '',
            'bucket' => '',
            'folder' => '',
            'requestHeaders' => array(),
        ]
    ],

Enable the storage driver by adding the key `dospaces` to Imager's `storages` config setting:

    'storages' => ['dospaces'],

Here's an example config, note that the endpoint has to be a complete URL with scheme, and as always you need to make sure that `imagerUrl` is pointed to the right location:

    'imagerUrl' => 'https://imager-test-bucket.ams3.digitaloceanspaces.com/transforms/',
    'storages' => ['dospaces'],
    'storageConfig' => [
        'dospaces'  => [
            'endpoint' => 'https://ams3.digitaloceanspaces.com',
            'accessKey' => 'MYACCESSKEY',
            'secretAccessKey' => 'MYSECRETKEY',
            'region' => 'ams3',
            'bucket' => 'imager-test-bucket',
            'folder' => 'transforms',
            'requestHeaders' => array(),
        ]
    ],
    
Also remember to always empty your Imager transforms cache when adding or removing external storages, as the transforms won't be uploaded if the transform already exists in the cache.
 

Price, license and support
---
The plugin is released under the MIT license. It requires Imager X Pro, which is a commercial plugin [available in the Craft plugin store](https://plugins.craftcms.com/imager-x). 
