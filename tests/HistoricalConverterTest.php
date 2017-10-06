<?php

namespace Tests\Jarobe\HistoricalMoney;

use Jarobe\HistoricalMoney\HistoricalConverter;
use Jarobe\HistoricalMoney\HistoricalExchange;
use Money\Currencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Money;
use Prophecy\Prophecy\ObjectProphecy;

final class HistoricalConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider convertExamples
     * @test
     */
    public function it_converts_to_a_different_currency(
        $dateString,
        $baseCurrencyCode,
        $counterCurrencyCode,
        $subunitBase,
        $subunitCounter,
        $ratio,
        $amount,
        $expectedAmount
    ) {
        $dateTime = new \DateTime($dateString);
        $baseCurrency = new Currency($baseCurrencyCode);
        $counterCurrency = new Currency($counterCurrencyCode);
        $pair = new CurrencyPair($baseCurrency, $counterCurrency, $ratio);

        /** @var Currencies|ObjectProphecy $currencies */
        $currencies = $this->prophesize(Currencies::class);

        /** @var HistoricalExchange|ObjectProphecy $exchange */
        $exchange = $this->prophesize(HistoricalExchange::class);

        $converter = new HistoricalConverter($currencies->reveal(), $exchange->reveal());

        $currencies->subunitFor($baseCurrency)->willReturn($subunitBase);
        $currencies->subunitFor($counterCurrency)->willReturn($subunitCounter);

        $exchange->quoteHistorical($dateTime, $baseCurrency, $counterCurrency)->willReturn($pair);

        $money = $converter->convertHistorical(
            $dateTime,
            new Money($amount, new Currency($baseCurrencyCode)),
            $counterCurrency
        );

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals($expectedAmount, $money->getAmount());
        $this->assertEquals($counterCurrencyCode, $money->getCurrency()->getCode());
    }

    public function convertExamples()
    {
        return [
            ['2017-09-01', 'USD', 'JPY', 2, 0, 101, 100, 101],
            ['2017-01-01', 'USD', 'JPY', 2, 0, 99, 100, 99],
            ['2000-00-01', 'JPY', 'USD', 0, 2, 0.0099, 1000, 990],
            ['2001-01-01', 'USD', 'EUR', 2, 2, 0.89, 100, 89],
            ['2000-01-01', 'EUR', 'USD', 2, 2, 1.12, 100, 112],
        ];
    }
}
