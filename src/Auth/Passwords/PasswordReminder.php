<?php

namespace Brouwers\LaravelDoctrine\Auth\Passwords;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="password_resets")
 */
class PasswordReminder
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $token;

    /**
     * @ORM\Column(name="created_at", type="CarbonDateTime", nullable=false)
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @param string $email
     * @param string $token
     */
    public function __construct($email, $token)
    {
        $this->email     = $email;
        $this->token     = $token;
        $this->createdAt = Carbon::now();
    }

    /**
     * Returns when the reminder was created.
     * @return Carbon
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
