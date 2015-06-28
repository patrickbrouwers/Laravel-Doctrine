<?php

namespace Brouwers\LaravelDoctrine\Console;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Exception;
use InvalidArgumentException;

class GenerateProxiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:generate:proxies
    {dest-path? : The path to generate your proxy classes. If none is provided, it will attempt to grab from configuration.}
    {-- filter=* : A string pattern used to match entities that should be processed.}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Generates proxy classes for entity classes.';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function fire()
    {
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        $metadatas = MetadataFilter::filter($metadatas, $this->option('filter'));

        // Process destination directory
        if (($destPath = $this->argument('dest-path')) === null) {
            $destPath = $this->em->getConfiguration()->getProxyDir();
        }

        if (!is_dir($destPath)) {
            mkdir($destPath, 0777, true);
        }

        $destPath = realpath($destPath);

        if (!file_exists($destPath)) {
            throw new InvalidArgumentException(
                sprintf("Proxies destination directory '<info>%s</info>' does not exist.",
                    $this->em->getConfiguration()->getProxyDir())
            );
        }

        if (!is_writable($destPath)) {
            throw new InvalidArgumentException(
                sprintf("Proxies destination directory '<info>%s</info>' does not have write permissions.", $destPath)
            );
        }

        if (count($metadatas)) {
            foreach ($metadatas as $metadata) {
                $this->comment(
                    sprintf('Processing entity "<info>%s</info>"', $metadata->name)
                );
            }

            // Generating Proxies
            $this->em->getProxyFactory()->generateProxyClasses($metadatas, $destPath);

            // Outputting information message
            $this->comment(PHP_EOL . sprintf('Proxy classes generated to "<info>%s</INFO>"', $destPath));
        } else {
            $this->error('No Metadata Classes to process.');
        }
    }
}
