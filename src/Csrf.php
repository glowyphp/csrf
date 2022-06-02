<?php

declare(strict_types=1);

namespace Glowy\Csrf;

use RuntimeException as CsrfException;

use function array_key_exists;
use function extension_loaded;
use function function_exists;
use function hash;
use function hash_equals;
use function is_array;
use function openssl_random_pseudo_bytes;
use function random_bytes;
use function session_status;

use const PHP_SESSION_ACTIVE;

class Csrf
{
    /**
     * Token name
     */
    protected string $tokenName = '';

    /**
     * Token value
     */
    protected string $tokenValue = '';

    /**
     * @param string $tokenNamePrefix  Prefix for CSRF token name.
     * @param string $tokenValuePrefix Prefix for CSRF token value.
     * @param int    $strength         Strength.
     *
     * @throws CsrfException
     */
    public function __construct(
        string $tokenNamePrefix = '__csrf_token',
        string $tokenValuePrefix = '',
        int $strength = 32
    ) {
        if ($strength < 32) {
            throw new CsrfException('Glowy Csrf instantiation failed. Minimum strength is 32.');
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new CsrfException(
                'Invalid CSRF storage. ' .
                'Use session_start() before instantiating the Glowy Csrf.'
            );
        }

        $this->tokenName = $tokenNamePrefix;

        if (isset($_SESSION[$this->tokenName])) {
            $this->tokenValue = $_SESSION[$this->tokenName];
        } else {
            $this->tokenValue = $tokenValuePrefix . $this->getRandomValue($strength);
            $_SESSION[$this->tokenName] = $this->tokenValue;
        }
    }

    /**
     * Returns a cryptographically secure random value.
     *
     * @param int $strength Strength.
     *
     * @throws CsrfException
     */
    protected function getRandomValue(int $strength): string
    {
        if (function_exists('random_bytes')) {
            return hash('sha512', random_bytes($strength));
        }

        if (extension_loaded('openssl')) {
            return hash('sha512', \openssl_random_pseudo_bytes($strength));
        }

        if (extension_loaded('mcrypt')) {
            return hash('sha512', \mcrypt_create_iv($strength, MCRYPT_DEV_URANDOM));
        }

        $message = 'Cannot generate cryptographically secure random values. '
                 . "Please install extension 'openssl' or 'mcrypt', or use "
                 . 'another cryptographically secure implementation.';

        throw new CsrfException($message);
    }

    /**
     * Checks whether an incoming CSRF token name and value is valid.
     *
     * @param string $value The incoming token value.
     *
     * @return bool True if valid, false if not.
     */
    public function isValid(string $value): bool
    {
        if (! isset($_SESSION[$this->getTokenName()])) {
            return false;
        }

        if (function_exists('hash_equals')) {
            return hash_equals($value, $_SESSION[$this->getTokenName()]);
        }

        return $value === $_SESSION[$this->getTokenName()];
    }

    /**
     * Get token name.
     */
    public function getTokenName(): string
    {
        return $this->tokenName;
    }

    /**
     * Get token value.
     */
    public function getTokenValue(): string
    {
        return $this->tokenValue;
    }
}
