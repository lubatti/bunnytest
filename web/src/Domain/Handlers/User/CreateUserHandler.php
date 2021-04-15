<?php
declare(strict_types=1);

namespace App\Domain\Handlers\User;

use App\Domain\Commands\User\CreateUserCommand;
use App\Domain\Entities\User;
use App\Domain\Services\CommunicationService;
use App\Infrastructure\Persistence\Doctrine\Repositories\UserRepository;
use Doctrine\ORM\Id\UuidGenerator;

class CreateUserHandler
{
    private UserRepository $userRepository;
    private CommunicationService $communicationService;

    public function __construct(UserRepository $userRepository, CommunicationService $communicationService)
    {
        $this->userRepository = $userRepository;
        $this->communicationService = $communicationService;
    }

    public function handle(CreateUserCommand $command)
    {
        $user = new User($command->getEmail(), $command->getPassword());

        $this->userRepository->em()->persist($user);
        $this->userRepository->em()->flush();

        $this->communicationService->spreadMessage([
            'event' => 'new_user_created',
            'data' => $user->toArray(),
        ]);
    }
}
