<?php

declare(strict_types=1);

namespace App\Command;

use App\Database\User;
use Cycle\ORM\EntityManagerInterface;
use Spiral\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends Command
{
    protected const NAME = 'create:user';

    protected const DESCRIPTION = 'Create an arena user';

    protected const ARGUMENTS = [
        ['username', InputArgument::REQUIRED]
    ];

    protected const OPTIONS = [];

    /**
     * Perform command
     */
    protected function perform(
        InputInterface $input,
        OutputInterface $output,
        EntityManagerInterface $entityManager
    ): void {
        $helper = $this->getHelper('question');

        $question = new Question('Please enter the users password: ');
        $question->setValidator(function ($value) {
            if (trim($value) == '') {
                throw new \Exception('The password cannot be empty');
            }

            return $value;
        });
        $question->setHidden(true);
        $question->setMaxAttempts(20);

        $password = $helper->ask($input, $output, $question);

        $u = new User();
        $u->name = 'Generated';
        $u->username = $this->argument('username');
        $u->password = password_hash((string) $password, PASSWORD_DEFAULT);

        $entityManager->persist($u)->run();
    }
}
