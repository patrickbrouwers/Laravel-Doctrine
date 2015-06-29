<?php

namespace Brouwers\LaravelDoctrine\Console;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\SchemaTool;

class SchemaCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:schema:create
    {--sql : Dumps the generated SQL statements to the screen (does not execute them)}
    {--em= : Create schema for a specific entity manager }';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Processes the schema and either create it directly on EntityManager Storage Connection or generate the SQL output.';

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

        if (!$this->option('sql')) {
            $this->error('ATTENTION: This operation should not be executed in a production environment.');
        }

        foreach ($names as $name) {
            $em   = $this->registry->getManager($name);
            $tool = new SchemaTool($em);

            $this->message('Creating database schema for <info>' . $name . '</info> entity manager...', 'blue');

            if ($this->option('sql')) {
                $sql = $tool->getCreateSchemaSql($em->getMetadataFactory()->getAllMetadata());
                $this->comment('     ' . implode(';     ' . PHP_EOL, $sql));
            } else {
                $tool->createSchema($em->getMetadataFactory()->getAllMetadata());
            }
        }

        $this->info('Database schema created successfully!');
    }
}
