<?php

namespace Jarobe\HistoricalMoney;

use Money\Currencies;
use Money\Currency;
use Money\Money;

/**
 * Provides a way to convert Money to Money in another Currency using a historical exchange rate.
 */
final class HistoricalConverter
{
    /**
     * @var Currencies
     */
    private $currencies;

    /**
     * @var HistoricalExchange
     */
    private $exchange;

    /**
     * @param Currencies         $currencies
     * @param HistoricalExchange $exchange
     */
    public function __construct(Currencies $currencies, HistoricalExchange $exchange)
    {
        $this->currencies = $currencies;
        $this->exchange = $exchange;
    }

    /**
     * @param \DateTimeInterface $dateTime
     * @param Money              $money
     * @param Currency           $counterCurrency
     * @param int                $roundingMode
     *
     * @return Money
     */
    public function convertHistorical(
        \DateTimeInterface $dateTime,
        Money $money,
        Currency $counterCurrency,
        $roundingMode = Money::ROUND_HALF_UP
    ) {
        $baseCurrency = $money->getCurrency();
        $ratio = $this->exchange->quoteHistorical($dateTime, $baseCurrency, $counterCurrency)->getConversionRatio();

        $baseCurrencySubunit = $this->currencies->subunitFor($baseCurrency);
        $counterCurrencySubunit = $this->currencies->subunitFor($counterCurrency);
        $subunitDifference = $baseCurrencySubunit - $counterCurrencySubunit;

        $ratio = $ratio / pow(10, $subunitDifference);

        $counterValue = $money->multiply($ratio, $roundingMode);

        return new Money($counterValue->getAmount(), $counterCurrency);
    }
}
