<?php

namespace Prokl\BitrixOrmBundle\Base\Exception;

use InvalidArgumentException as CommonInvalidArgumentException;

class InvalidArgumentException extends CommonInvalidArgumentException implements BitrixOrmExceptionInterface
{
}
