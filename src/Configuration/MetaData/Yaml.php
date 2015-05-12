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
        $this->settings = [
            'paths'      => $settings['paths'],
            'dev'        => $dev,
            'proxy_path' => @$settings['proxies']['path']
        ];

        return $this;
    }

    /**
     * @return \Doctrine\ORM\Configuration|mixed
     */
    public function resolve()
    {
        return Setup::createYAMLMetadataConfiguration(
            $this->settings['paths'],
            $this->settings['dev'],
            $this->settings['proxy_path'],
            $this->getCache()
        );
    }
}
