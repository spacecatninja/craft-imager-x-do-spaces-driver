<?php
/**
 * Digital Ocean Spaces external storage driver for Imager X
 *
 * @link      https://www.spacecat.ninja/
 * @copyright Copyright (c) 2022 André Elvan
 */

namespace spacecatninja\imagerxdospacesdriver;

use craft\base\Plugin;

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
    public function init(): void
    {
        parent::init();
        
        Event::on(\spacecatninja\imagerx\ImagerX::class,
            \spacecatninja\imagerx\ImagerX::EVENT_REGISTER_EXTERNAL_STORAGES,
            static function(\spacecatninja\imagerx\events\RegisterExternalStoragesEvent $event) {
                $event->storages['dospaces'] = DOSpacesStorage::class;
            }
        );
    }
}
