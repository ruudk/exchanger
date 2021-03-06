<?php

namespace Exchanger\Service;

use Exchanger\Contract\ExchangeRateQuery;
use Exchanger\Contract\HistoricalExchangeRateQuery;
use Exchanger\Exception\UnsupportedCurrencyPairException;
use Exchanger\Exception\UnsupportedDateException;
use Exchanger\ExchangeRate;
use Exchanger\Service\HistoricalService;
use Exchanger\StringUtil;

/**
 * Russian Central Bank Service.
 *
 */
class RussianCentralBank extends HistoricalService
{
    const URL = 'http://www.cbr.ru/scripts/XML_daily.asp';

    /**
     * {@inheritdoc}
     */
    protected function getLatestExchangeRate(ExchangeRateQuery $exchangeQuery)
    {
        $baseCurrency = $exchangeQuery->getCurrencyPair()->getBaseCurrency();

        $content = $this->request(self::URL);
        $element = StringUtil::xmlToElement($content);

        $elements = $element->xpath('./Valute[CharCode="'.$baseCurrency.'"]');

        if (empty($elements)) {
            throw new UnsupportedCurrencyPairException($exchangeQuery->getCurrencyPair(), $this);
        }

        $date = \DateTime::createFromFormat('!d.m.Y', (string) $element['Date']);
        $rate = str_replace(',', '.', (string) $elements['0']->Value);

        return new ExchangeRate($rate, $date);
    }

    /**
     * {@inheritdoc}
     */
    protected function getHistoricalExchangeRate(HistoricalExchangeRateQuery $exchangeQuery)
    {
        $baseCurrency = $exchangeQuery->getCurrencyPair()->getBaseCurrency();
        $formattedDate = $exchangeQuery->getDate()->format('d.m.Y');

        $content = $this->request(self::URL . '?' . http_build_query(['date_req' => $formattedDate]));
        $element = StringUtil::xmlToElement($content);

        $elements = $element->xpath('./Valute[CharCode="'.$baseCurrency.'"]');

        if (empty($elements)) {
            if ((string) $element['Date'] !== $exchangeQuery->getDate()->format('d.m.Y')) {
                throw new UnsupportedDateException($exchangeQuery->getDate(), $this);
            }

            throw new UnsupportedCurrencyPairException($exchangeQuery->getCurrencyPair(), $this);
        }

        $rate = str_replace(',', '.', (string) $elements['0']->Value);

        return new ExchangeRate($rate, $exchangeQuery->getDate());
    }

    /**
     * {@inheritdoc}
     */
    public function supportQuery(ExchangeRateQuery $exchangeQuery)
    {
        return 'RUB' === $exchangeQuery->getCurrencyPair()->getQuoteCurrency();
    }
}
