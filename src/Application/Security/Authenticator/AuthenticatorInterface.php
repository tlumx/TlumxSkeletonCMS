<?php

namespace Application\Security\Authenticator;

interface AuthenticatorInterface
{
    public function authenticate($identity, $credential);
}