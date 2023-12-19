<?php

namespace App\Application\Transformer;

use League\Fractal\TransformerAbstract;
use IntlDateFormatter;

class BaseTransformer extends TransformerAbstract
{
  protected function formatDateTime(\DateTime $dateTime): string
  {
    $formatter = new IntlDateFormatter(
      'en_US', // Locale
      IntlDateFormatter::FULL, // Date type
      IntlDateFormatter::FULL, // Time type
      'UTC', // Timezone
      IntlDateFormatter::GREGORIAN, // Calendar type
      'yyyy-MM-dd\'T\'HH:mm:ss.SSS\'Z\'' // Pattern
    );

    return $formatter->format($dateTime);
  }
}
