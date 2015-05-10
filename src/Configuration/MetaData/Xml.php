<?php

namespace Brouwers\LaravelDoctrine\Configuration\MetaData;

use Doctrine\ORM\Tools\Setup;

class Xml extends AbstractMetaData
{
    /**
     * @var string
     */
    protected $name = 'xml';

    /**
     * @param array $settings
     * @param bool  $dev
     *
     * @return static
     */
    public function configure(array $settings = [], $dev = false)
    {
        return new static(Setup::createXMLMetadataConfiguration(
            $settings['paths'],
            $dev,
            @$settings['proxies']['path'],
            $this->getCache()
        ));
    }

    /**
     * @return mixed
     */
    public function getCache()
    {
        // TODO: Implement getCache() method.
    }
}
