<?php

namespace Jarobe\HistoricalMoney\Exception;

use Money\Exception as MoneyException;

/**
 * Throws when a currency pair is requested for an unsupported historical date.
 */
class UnsupportedHistoricalDateException extends \InvalidArgumentException implements MoneyException
{
    /**
     * @param \DateTimeInterface $dateTime
     *
     * @return UnsupportedHistoricalDateException
     */
    public static function create(\DateTimeInterface $dateTime)
    {
        $message = sprintf(
            'No rate provided for date: %s',
            $dateTime->format('Y-m-d G:i:s')
        );

        return new self($message);
    }
}
