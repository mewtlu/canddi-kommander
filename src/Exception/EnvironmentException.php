<?php

namespace CanddiKommander\Exception;

class EnvironmentException extends ApplicationException
{
    public const CODE_COMPLETE_ENV_FAILURE = 1;
    public const CODE_MISSING_ENV = 2;
}
