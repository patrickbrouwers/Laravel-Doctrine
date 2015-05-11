<?php

namespace Brouwers\LaravelDoctrine\Configuration\MetaData;

use Doctrine\ORM\Tools\Setup;

class Annotations extends AbstractMetaData
{
    /**
     * @var string
     */
    protected $name = 'annotations';

    /**
     * @param array $settings
     * @param bool  $dev
     *
     * @return static
     */
    public function configure(array $settings = [], $dev = false)
    {
        return new static(Setup::createAnnotationMetadataConfiguration(
            $settings['paths'],
            $dev,
            @$settings['proxies']['path'],
            $this->getCache(),
            isset($settings['simple']) ? $settings['simple'] : false
        ));
    }
}
