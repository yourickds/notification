<?php

namespace App\Services\Notification\Exceptions;

use Exception;

/**
 * 4xx, валидация, неверные данные, permanent block.
 * Retry НЕ производится.
 */
class FatalChannelException extends Exception {}
