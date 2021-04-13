<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Adapters\User\CreateUserAdapter;
use App\Infrastructure\CommandBus\CommandBusInterface;
use Psr\Http\Message\ResponseInterface as Response;

class CreateUserAction extends Action
{
    private CreateUserAdapter $adapter;
    private CommandBusInterface $commandBus;

    public function __construct(CommandBusInterface $commandBus, CreateUserAdapter $adapter)
    {
        $this->commandBus = $commandBus;
        $this->adapter = $adapter;
    }

    protected function action(): Response
    {
        $command = $this->adapter->adaptPsrRequest($this->request);

        $this->commandBus->handle($command);

        return $this->respondWithData([]);
    }
}
