<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Infrastructure\Persistence\Doctrine\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends Action
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');
        $user = $this->userRepository->findOrFail($userId);

        return $this->respondWithData($user);
    }
}
