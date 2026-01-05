<?php

namespace ComfortSmtpScoped\Illuminate\Contracts\Container;

use Exception;
use ComfortSmtpScoped\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
