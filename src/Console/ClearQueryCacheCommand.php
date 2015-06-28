<?php

namespace Brouwers\LaravelDoctrine\Console;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\XcacheCache;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use LogicException;

class ClearQueryCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:clear:query:cache
    {--flush : If defined, cache entries will be flushed instead of deleted/invalidated.}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Clear all query cache of the various cache drivers.';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Doctrine\Common\Cache\Cache|null
     */
    protected $cache;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em    = $em;
        $this->cache = $em->getConfiguration()->getQueryCacheImpl();
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        if (!$this->cache) {
            throw new InvalidArgumentException('No Query cache driver is configured on given EntityManager.');
        }

        if ($this->cache instanceof ApcCache) {
            throw new LogicException("Cannot clear APC Cache from Console, its shared in the Webserver memory and not accessible from the CLI.");
        }

        if ($this->cache instanceof XcacheCache) {
            throw new LogicException("Cannot clear XCache Cache from Console, its shared in the Webserver memory and not accessible from the CLI.");
        }

        $this->message('Clearing query cache entries');

        $result  = $this->cache->deleteAll();
        $message = ($result) ? 'Successfully deleted cache entries.' : 'No cache entries were deleted.';

        if ($this->option('flush')) {
            $result  = $this->cache->flushAll();
            $message = ($result) ? 'Successfully flushed cache entries.' : $message;
        }

        $this->info($message);
    }
}
