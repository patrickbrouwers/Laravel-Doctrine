<?php

namespace Brouwers\LaravelDoctrine\Configuration\MetaData;

use Brouwers\LaravelDoctrine\Configuration\MetaData\Config\ConfigDriver;
use Doctrine\ORM\Tools\Setup;

class Config extends AbstractMetaData
{
    /**
     * @var string
     */
    protected $name = 'config';

    /**
     * @param array $settings
     * @param bool  $dev
     *
     * @return static
     */
    public function configure(array $settings = [], $dev = false)
    {
        $this->settings = [
            'mapping_file' => $settings['mapping_file'],
            'dev'          => $dev,
            'proxy_path'   => @$settings['proxies']['path']
        ];

        return $this;
    }

    /**
     * @return \Doctrine\ORM\Configuration|mixed
     */
    public function resolve()
    {
        $configuration = Setup::createConfiguration(
            $this->settings['dev'],
            $this->settings['proxy_path'],
            $this->getCache()
        );

        $configuration->setMetadataDriverImpl(
            new ConfigDriver(
                config($this->settings['mapping_file'], [])
            )
        );

        return $configuration;
    }
}
