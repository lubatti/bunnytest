<?php
declare(strict_types=1);

namespace App\Domain\Handlers\User;

use App\Domain\Commands\User\CreateUserCommand;
use App\Domain\Services\CommunicationService;

class CreateUserHandler
{
    private CommunicationService $communicationService;

    public function __construct(CommunicationService $communicationService)
    {
        $this->communicationService = $communicationService;
    }

    public function handle(CreateUserCommand $command)
    {
        $this->communicationService->spreadMessage([]);
    }
}
