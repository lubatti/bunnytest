<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Infrastructure\Persistence\Doctrine\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListUsersAction extends Action
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function action(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->respondWithData($users);
    }
}
