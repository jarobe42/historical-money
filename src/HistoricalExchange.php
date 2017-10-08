<?php

namespace Jarobe\HistoricalMoney;

use Jarobe\HistoricalMoney\Exception\UnsupportedHistoricalDateException;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;

/**
 * Provides a way to get a historical exchange rate from a third-party source and
 * return a currency pair.
 */
interface HistoricalExchange
{
    /**
     * Returns a currency pair for the passed currencies with the rate coming from a
     * third-party source.
     *
     * @param \DateTimeInterface $dateTime
     * @param Currency           $baseCurrency
     * @param Currency           $counterCurrency
     *
     * @return CurrencyPair
     *
     * @throws UnsupportedHistoricalDateException When the provided date is not supported
     * @throws UnresolvableCurrencyPairException  When there is no currency pair available for the currencies
     */
    public function quoteHistorical(\DateTimeInterface $dateTime, Currency $baseCurrency, Currency $counterCurrency);
}
