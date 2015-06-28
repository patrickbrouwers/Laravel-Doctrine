<?php

namespace Brouwers\LaravelDoctrine\Console;

use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\SchemaTool;

class SchemaDropCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:schema:drop
    {--sql : Instead of trying to apply generated SQLs into EntityManager Storage Connection, output them.}
    {--force : Don\'t ask for the deletion of the database, but force the operation to run.}
    {--full : Instead of using the Class Metadata to detect the database table schema, drop ALL assets that the database contains. }';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Drop the complete database schema of EntityManager Storage Connection or generate the corresponding SQL output.';

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
        if ($this->option('sql')) {
            if ($this->option('full')) {
                $sql = $this->tool->getDropDatabaseSQL();
            } else {
                $sql = $this->tool->getDropSchemaSQL($this->factory->getAllMetadata());
            }
            $this->comment('     ' . implode(';     ' . PHP_EOL, $sql));

            return;
        }

        if ($this->option('force')) {
            $this->message('Dropping database schema...');

            if ($this->option('full')) {
                $this->tool->dropDatabase();
            } else {
                $this->tool->dropSchema($this->factory->getAllMetadata());
            }

            $this->info('Database schema dropped successfully!');

            return;
        }

        $this->error('ATTENTION: This operation should not be executed in a production environment.');

        if ($this->option('full')) {
            $sql = $this->tool->getDropDatabaseSQL();
        } else {
            $sql = $this->tool->getDropSchemaSQL($this->factory->getAllMetadata());
        }

        if (count($sql)) {
            $this->info('');
            $pluralization = (1 === count($sql)) ? 'query was' : 'queries were';
            $this->message(sprintf('The Schema-Tool would execute <info>"%s"</info> %s to update the database.', count($sql), $pluralization));
            $this->message('Please run the operation by passing one - or both - of the following options:');

            $this->comment(sprintf('    <info>php artisan %s --force</info> to execute the command', $this->getName()));
            $this->comment(sprintf('    <info>php artisan %s --sql</info> to dump the SQL statements to the screen', $this->getName()));

            return;
        }

        $this->info('');
        $this->error('Nothing to drop. The database is empty!');
    }
}
