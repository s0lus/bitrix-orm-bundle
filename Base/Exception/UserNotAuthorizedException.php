<?php

namespace Prokl\BitrixOrmBundle\Base\Exception;

use RuntimeException;

class UserNotAuthorizedException extends RuntimeException implements BitrixOrmExceptionInterface
{
}
