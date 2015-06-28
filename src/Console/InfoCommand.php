<?php

namespace Brouwers\LaravelDoctrine\Console;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

class InfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:info';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Show basic information about all mapped entities.';

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
        $entityClassNames = $this->em->getConfiguration()
                                     ->getMetadataDriverImpl()
                                     ->getAllClassNames();

        if (!$entityClassNames) {
            throw new Exception(
                'You do not have any mapped Doctrine ORM entities according to the current configuration. ' .
                'If you have entities or mapping files you should check your mapping configuration for errors.'
            );
        }

        $this->message(sprintf("Found <info>%d</info> mapped entities:", count($entityClassNames)));

        foreach ($entityClassNames as $entityClassName) {
            try {
                $this->em->getClassMetadata($entityClassName);
                $this->comment(sprintf("<info>[OK]</info>   %s", $entityClassName));
            } catch (MappingException $e) {
                $this->comment("<error>[FAIL]</error> " . $entityClassName);
                $this->comment(sprintf("<comment>%s</comment>", $e->getMessage()));
                $this->comment('');
            }
        }
    }
}
