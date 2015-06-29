<?php

namespace Brouwers\LaravelDoctrine\Console;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class EnsureProductionSettingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:ensure:production
    {--with-db : Flag to also inspect database connection existence.}
    {--em= : Ensure production settings for a specific entity manager }';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Verify that Doctrine is properly configured for a production environment.';

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
     * @return void
     */
    public function fire()
    {
        $names = $this->option('em') ? [$this->option('em')] : $this->registry->getManagerNames();

        foreach ($names as $name) {
            $em = $this->registry->getManager($name);

            try {
                $em->getConfiguration()->ensureProductionSettings();

                if ($this->option('with-db')) {
                    $em->getConnection()->connect();
                }
            } catch (Exception $e) {
                $this->error('Error for ' . $name . ' entity manager');
                $this->error($e->getMessage());

                return;
            }

            $this->comment('Environment for <info>' . $name . '</info> entity manager is correctly configured for production.');
        }
    }
}
