<?php

namespace Brouwers\LaravelDoctrine\Console;

use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

class InfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:info
    {--em= : Info for a specific entity manager }';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Show basic information about all mapped entities.';

    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @param ManagerRegistry $registry
     *
     * @internal param EntityManagerInterface $em
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct();
        $this->registry = $registry;
    }

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function fire()
    {
        $names = $this->option('em') ? [$this->option('em')] : $this->registry->getManagerNames();

        foreach ($names as $name) {
            $em = $this->registry->getManager($name);

            $entityClassNames = $em->getConfiguration()
                                   ->getMetadataDriverImpl()
                                   ->getAllClassNames();

            if (!$entityClassNames) {
                throw new Exception(
                    'You do not have any mapped Doctrine ORM entities according to the current configuration. ' .
                    'If you have entities or mapping files you should check your mapping configuration for errors.'
                );
            }

            $this->message(sprintf("Found <info>%d</info> mapped entities for <info>{$name}</info> entity manager:", count($entityClassNames)));

            foreach ($entityClassNames as $entityClassName) {
                try {
                    $em->getClassMetadata($entityClassName);
                    $this->comment(sprintf("<info>[OK]</info>   %s", $entityClassName));
                } catch (MappingException $e) {
                    $this->comment("<error>[FAIL]</error> " . $entityClassName);
                    $this->comment(sprintf("<comment>%s</comment>", $e->getMessage()));
                    $this->comment('');
                }
            }
        }
    }
}
