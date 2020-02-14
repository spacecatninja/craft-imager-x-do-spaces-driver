<?php
/**
 * Digital Ocean Spaces external storage driver for Imager X
 *
 * @link      https://www.spacecat.ninja/
 * @copyright Copyright (c) 2020 André Elvan
 */

namespace spacecatninja\imagerxdospacesdriver;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;

use spacecatninja\imagerxdospacesdriver\externalstorage\DOSpacesStorage;

use yii\base\Event;

/**
 * @author    SpaceCatNinja
 * @package   ImagerXDoSpacesDriver
 * @since     2.0.0
 *
 */
class ImagerXDoSpacesDriver extends Plugin
{
    public static $plugin;

    public function init()
    {
        parent::init();
        self::$plugin = $this;
        
        Event::on(\spacecatninja\imagerx\ImagerX::class,
            \spacecatninja\imagerx\ImagerX::EVENT_REGISTER_EXTERNAL_STORAGES,
            static function (\spacecatninja\imagerx\events\RegisterExternalStoragesEvent $event) {
                $event->storages['dospaces'] = DOSpacesStorage::class;
            }
        );
    }
}
