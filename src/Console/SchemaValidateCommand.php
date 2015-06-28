<?php

namespace Brouwers\LaravelDoctrine\Console;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;

class SchemaValidateCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:schema:validate
    {--skip-mapping : Skip the mapping validation check}
    {--skip-sync : Skip checking if the mapping is in sync with the database}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Validate the mapping files.';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var SchemaValidator
     */
    protected $validator;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em        = $em;
        $this->validator = new SchemaValidator($this->em);
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        if ($this->option('skip-mapping')) {
            $this->comment('Mapping]  Skipped mapping check.');
        } elseif ($errors = $this->validator->validateMapping()) {
            foreach ($errors as $className => $errorMessages) {
                $this->error("[Mapping]  FAIL - The entity-class '" . $className . "' mapping is invalid:");
                $this->comment('');

                foreach ($errorMessages as $errorMessage) {
                    $this->message('* ' . $errorMessage, 'red');
                }
            }
        } else {
            $this->info('[Mapping]  OK - The mapping files are correct.');
        }

        if ($this->option('skip-sync')) {
            $this->comment('Database] SKIPPED - The database was not checked for synchronicity.');
        } elseif (!$this->validator->schemaInSyncWithMetadata()) {
            $this->error('[Database] FAIL - The database schema is not in sync with the current mapping file.');
        } else {
            $this->info('[Database] OK - The database schema is in sync with the mapping files.');
        }
    }
}
