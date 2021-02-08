<?php

declare(strict_types=1);

namespace Atomastic\Csrf;

use RuntimeException as CsrfException;

class Csrf
{
    /**
     * Token name
     *
     * @var string
     */
    protected $tokenName;

    /**
     * Token value
     *
     * @var string
     */
    protected $tokenValue;

    /**
     * Constructor.
     *
     * @param string  $tokenNamePrefix  Prefix for CSRF token name.
     * @param string  $tokenValuePrefix Prefix for CSRF token value.
     * @param int     $strength         Strength.
     *
     * @throws CsrfException
     */
    public function __construct(string $tokenNamePrefix = '_csrf_name_',
                                string $tokenValuePrefix = '_csrf_value_',
                                int $strength = 32)
    {
        if ($strength < 32) {
            throw new CsrfException('Atomastic Csrf instantiation failed. Minimum strength is 32.');
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new CsrfException(
                'Invalid CSRF storage. ' .
                'Use session_start() before instantiating the Atomastic Csrf.'
            );
        }

        $this->tokenName  = $tokenNamePrefix  . $this->getRandomValue($strength);
        $this->tokenValue = $tokenValuePrefix . $this->getRandomValue($strength);

        if (!array_key_exists($this->tokenName, $_SESSION) || !is_array($_SESSION[$this->tokenName])) {
            $_SESSION[$this->tokenName] = $this->tokenValue;
        }
    }

    /**
     *
     * Returns a cryptographically secure random value.
     *
     * @param int $strength Strength.
     *
     * @throws CsrfException
     *
     * @return string
     */
    protected function getRandomValue(int $strength): string
    {
        if (function_exists('random_bytes')) {
            return hash('sha512', random_bytes($strength));
        }

        if (extension_loaded('openssl')) {
            return hash('sha512', openssl_random_pseudo_bytes($strength));
        }

        if (extension_loaded('mcrypt')) {
            return hash('sha512', mcrypt_create_iv($strength, MCRYPT_DEV_URANDOM));
        }

        $message = "Cannot generate cryptographically secure random values. "
                 . "Please install extension 'openssl' or 'mcrypt', or use "
                 . "another cryptographically secure implementation.";

        throw new CsrfException($message);
    }

    /**
     * Checks whether an incoming CSRF token name and value is valid.
     *
     * @param string $name  The incoming token name.
     * @param string $value The incoming token value.
     *
     * @return bool True if valid, false if not.
     */
    public function isValid(string $name, string $value): bool
    {
        if (!isset($_SESSION[$name])) {
            return false;
        }

        if (function_exists('hash_equals')) {
            return hash_equals($value, $_SESSION[$this->getTokenName()]);
        }

        return $value === $_SESSION[$this->getTokenName()];
    }

    /**
     * Get token name.
     *
     * @return string
     */
    public function getTokenName(): string
    {
        return $this->tokenName;
    }

    /**
     * Get token value.
     *
     * @return string
     */
    public function getTokenValue(): string
    {
        return $this->tokenValue;
    }
}
