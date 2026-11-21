<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\OAuth;

use RuntimeException;

/**
 * A login attempt that failed; the message is safe to show to the user.
 */
class LoginFailedException extends RuntimeException {

}
