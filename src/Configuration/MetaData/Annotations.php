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
        $this->settings = [
            'paths'      => $settings['paths'],
            'dev'        => $dev,
            'proxy_path' => @$settings['proxies']['path'],
            'simple'     => isset($settings['simple']) ? $settings['simple'] : false
        ];

        return $this;
    }

    /**
     * @return \Doctrine\ORM\Configuration|mixed
     */
    public function resolve()
    {
        return Setup::createAnnotationMetadataConfiguration(
            $this->settings['paths'],
            $this->settings['dev'],
            $this->settings['proxy_path'],
            $this->getCache(),
            $this->settings['simple']
        );
    }
}
