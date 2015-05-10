<?php

namespace Brouwers\LaravelDoctrine\Configuration;

interface Driver
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     */
    public function setName($name);
}
