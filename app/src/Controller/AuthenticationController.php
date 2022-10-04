<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\User;
use App\Repository\UserRepository;
use App\Request\LoginRequest;
use Spiral\Http\Exception\ClientException\ForbiddenException;
use Spiral\Prototype\Traits\PrototypeTrait;

class AuthenticationController
{
    use PrototypeTrait;

    /**
     * @var UserRepository
     */
    protected $users;

    public function __construct()
    {
        $this->users = $this->orm->getRepository('user');
    }


    public function index()
    {
        return $this->views->render('login.dark.php');
    }


    public function login(LoginRequest $login)
    {
        if (!$login->isValid()) {
            return [
                'status' => 400,
                'errors' => $login->getErrors()
            ];
        }

        /** @var User $user */
        $user = $this->users->findOne(['username' => $login->getField('username')]);
        if (
            $user === null
            || !password_verify((string) $login->getField('password'), $user->password)
        ) {
            return [
                'status' => 400,
                'error'  => 'No such user'
            ];
        }

        //create token
        $this->auth->start(
            $this->authTokens->create(['userID' => $user->getUuid()])
        );

        return $this->response->redirect('/');
    }

    public function checkAuthenticated()
    {
        if ($this->auth->getActor() === null) {
            throw new ForbiddenException();
        }

        dump($this->auth->getActor());
    }

    public function logout()
    {
        $this->auth->close();
        return $this->response->redirect('/');
    }
}
