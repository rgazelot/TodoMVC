<?php

namespace TodoMVC\TodoMVCBundle\Service;

use InvalidArgumentException;

use TodoMVC\TodoMVCBundle\Service\Mailer,
    TodoMVC\TodoMVCBundle\Entity\User;

class UserHandler
{
    /**
     * @var \TodoMVC\TodoMVCBundle\Service\Mailer
     */
    private $mailer;

    /**
     * @param \TodoMVC\TodoMVCBundle\Service\Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function create($email, $password, $confirmPassword)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        $parts = explode('@', $email);

        if (!in_array($parts[1], $this->getAuthorizedNetworks())) {
            throw new InvalidArgumentException('Email must belong an authorized network');
        }

        if ($password !== $confirmPassword) {
            throw new InvalidArgumentException('Password and confirmPassword must be equals');
        }

        $user = new User($email, $password);

        $this->mailer->send($user, 'user_welcome');
    }

    private function getAuthorizedNetworks()
    {
        return [
            'hetic.net',
        ];
    }
}
