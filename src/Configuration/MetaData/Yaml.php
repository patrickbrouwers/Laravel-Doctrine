<?php

namespace Brouwers\LaravelDoctrine\Configuration\MetaData;

use Doctrine\ORM\Tools\Setup;

class Yaml extends AbstractMetaData
{
    /**
     * @var string
     */
    protected $name = 'yaml';

    /**
     * @param array $settings
     * @param bool  $dev
     *
     * @return static
     */
    public function configure(array $settings = [], $dev = false)
    {
        return new static(Setup::createYAMLMetadataConfiguration(
            $settings['paths'],
            $dev,
            @$settings['proxies']['path'],
            $this->getCache()
        ));
    }
}
