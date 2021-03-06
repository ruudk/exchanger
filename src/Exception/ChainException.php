<?php

/*
 * This file is part of Exchanger.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Exchanger\Exception;

/**
 * Exception thrown by the Chain Service.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class ChainException extends Exception
{
    private $exceptions;

    /**
     * Creates a new chain exception.
     *
     * @param Exception[] $exceptions
     */
    public function __construct(array $exceptions)
    {
        $this->exceptions = $exceptions;
    }

    /**
     * Gets the exceptions indexed by service class name.
     *
     * @return Exception[]
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }
}
