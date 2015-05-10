<?php

use Brouwers\LaravelDoctrine\Configuration\MetaData\Annotations;
use Brouwers\LaravelDoctrine\Configuration\MetaData\CustomMetaData;
use Brouwers\LaravelDoctrine\Configuration\MetaData\MetaDataManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\Setup;

class MetaDataManagerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        MetaDataManager::registerDrivers([
            'annotations' => [
                'paths' => []
            ]
        ]);
    }

    public function test_register_metadatas()
    {
        $drivers = MetaDataManager::getDrivers();
        $this->assertCount(1, $drivers);
        $this->assertInstanceOf(Annotations::class, head($drivers));
    }

    public function test_metadata_can_be_extended()
    {
        MetaDataManager::extend('annotations', function ($driver) {

            // Should give instance of the already registered driver
            $this->assertInstanceOf(Annotations::class, $driver);

            return $driver;
        });

        $driver   = MetaDataManager::resolve('annotations');

        $this->assertInstanceOf(Configuration::class, $driver->getConfig());
    }

    public function test_custom_metadata_can_be_set()
    {
        MetaDataManager::extend('custom', function () {
            return Setup::createAnnotationMetadataConfiguration([], false);
        });

        $driver = MetaDataManager::resolve('custom');
        $this->assertInstanceOf(CustomMetaData::class, $driver);
        $this->assertInstanceOf(Configuration::class, $driver->getConfig());
    }

    public function test_a_custom_class_can_be_returned_while_extending()
    {
        MetaDataManager::extend('custom2', function () {
            return new CustomMetaData();
        });

        $driver = MetaDataManager::resolve('custom2');
        $this->assertInstanceOf(CustomMetaData::class, $driver);
    }

    public function test_a_string_class_can_be_use_as_extend()
    {
        MetaDataManager::extend('custom3', CustomMetaData::class);

        $driver = MetaDataManager::resolve('custom3');
        $this->assertInstanceOf(CustomMetaData::class, $driver);
    }
}
