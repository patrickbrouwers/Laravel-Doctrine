<?php

namespace Brouwers\LaravelDoctrine\Configuration\MetaData;

class CustomMetaData extends AbstractMetaData
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $settings
     * @param bool  $dev
     *
     * @return static
     */
    public function configure(array $settings = [], $dev = false)
    {
        return $this;
    }
}
