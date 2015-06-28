<?php

namespace Brouwers\LaravelDoctrine\Console;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

class EnsureProductionSettingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:ensure:production
    {--with-db : Flag to also inspect database connection existence.}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Verify that Doctrine is properly configured for a production environment.';

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
     * @return void
     */
    public function fire()
    {
        try {
            $this->em->getConfiguration()->ensureProductionSettings();

            if ($this->option('with-db')) {
                $this->em->getConnection()->connect();
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return;
        }

        $this->info('Environment is correctly configured for production.');
    }
}
