<?php

namespace Jarobe\HistoricalMoney\Exchange;

use Jarobe\HistoricalMoney\Exception\UnsupportedHistoricalDateException;
use Jarobe\HistoricalMoney\HistoricalExchange;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Swap\Swap;
use Exchanger\Exception\Exception as ExchangerException;

final class HistoricalSwapExchange implements HistoricalExchange
{
    /**
     * @var Swap
     */
    private $swap;

    /**
     * @param Swap $swap
     */
    public function __construct(Swap $swap)
    {
        $this->swap = $swap;
    }

    /**
     * Returns a currency pair for the passed currencies with the rate coming from a third-party source.
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
    public function quoteHistorical(\DateTimeInterface $dateTime, Currency $baseCurrency, Currency $counterCurrency)
    {
        try {
            $rate = $this->swap->historical($baseCurrency->getCode().'/'.$counterCurrency->getCode(), $dateTime);
        } catch (ExchangerException $e) {
            throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
        }

        return new CurrencyPair($baseCurrency, $counterCurrency, $rate->getValue());
    }
}
