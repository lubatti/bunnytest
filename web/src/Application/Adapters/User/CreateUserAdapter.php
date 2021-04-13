<?php
declare(strict_types=1);

namespace App\Application\Adapters\User;

use App\Domain\Commands\User\CreateUserCommand;
use App\Domain\User\UserNotFoundException;
use App\Infrastructure\CommandBus\CommandInterface;
use App\Infrastructure\Persistence\Doctrine\Repositories\UserRepository;
use Slim\Psr7\Request;
use Exception;

class CreateUserAdapter
{
    private const FIELD_EMAIL = 'email';
    private const FIELD_PASSWORD = 'password';

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function adaptPsrRequest(Request $request): CommandInterface
    {
        $body = $request->getParsedBody();

        $this->assertValidContent($body);

        return new CreateUserCommand($body[self::FIELD_EMAIL], $body[self::FIELD_PASSWORD]);
    }

    /**
     * @throws Exception
     */
    private function assertValidContent(array $content): void
    {
        try {
            $user = $this->userRepository->findOneBy(['email' => $content[self::FIELD_EMAIL]]);

            if ($user) {
                throw new Exception('Email already used');
            }
        } catch (UserNotFoundException $e) {
            return;
        }
    }
}
