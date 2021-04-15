<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Persistence\Doctrine\Repositories\UserRepository")
 * @ORM\Table(name="users")
 */
class User
{
    private const PASSWORD_ALGORITHM = PASSWORD_BCRYPT;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     */
    protected string $id;

    /**
     * @ORM\Column(type="string", name="email", length=100, nullable=false)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", name="password", length=100, nullable=false)
     */
    private string $password;

    public function __construct(string $email, string $password)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->email = $email;
        $this->setPassword($password);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, static::PASSWORD_ALGORITHM);
    }

    public function passwordVerified(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
        ];
    }
}
