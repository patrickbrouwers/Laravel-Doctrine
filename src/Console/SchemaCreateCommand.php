<?php

namespace Brouwers\LaravelDoctrine\Console;

use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\SchemaTool;

class SchemaCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:schema:create
    {--sql : Dumps the generated SQL statements to the screen (does not execute them)}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Processes the schema and either create it directly on EntityManager Storage Connection or generate the SQL output.';

    /**
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    protected $tool;

    /**
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    protected $factory;

    /**
     * @param SchemaTool           $tool
     * @param ClassMetadataFactory $factory
     */
    public function __construct(SchemaTool $tool, ClassMetadataFactory $factory)
    {
        parent::__construct();
        $this->tool    = $tool;
        $this->factory = $factory;
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        $this->message('Creating database schema...', 'blue');

        if ($this->option('sql')) {
            $sql = $this->tool->getCreateSchemaSql($this->factory->getAllMetadata());
            $this->comment('     ' . implode(';     ' . PHP_EOL, $sql));
        } else {
            $this->error('ATTENTION: This operation should not be executed in a production environment.');
            $this->tool->createSchema($this->factory->getAllMetadata());
            $this->info('Database schema created successfully!');
        }
    }
}
