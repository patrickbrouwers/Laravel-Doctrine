<?php

namespace Brouwers\LaravelDoctrine\Extensions\SoftDeletes;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

trait SoftDeletes
{
    /**
     * @ORM\Column(name="deleted_at", type="CarbonDateTime", nullable=true)
     * @var Carbon
     */
    protected $deletedAt;

    /**
     * @return Carbon
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param Carbon $deletedAt
     */
    public function setDeletedAt(Carbon $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return Carbon::now() > $this->deletedAt;
    }
}
