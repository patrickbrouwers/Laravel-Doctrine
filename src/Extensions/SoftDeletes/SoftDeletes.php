<?php

namespace Brouwers\LaravelDoctrine\Extensions\SoftDeletes;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait SoftDeletes
{
    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $deleted_at;

    /**
     * @return DateTime
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * @param DateTime $deleted_at
     */
    public function setDeletedAt(DateTime $deleted_at)
    {
        $this->deleted_at = $deleted_at;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return new DateTime > $this->deleted_at;
    }
}
