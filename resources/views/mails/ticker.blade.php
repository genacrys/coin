Sentiment: {{ number_format($old->compare($new, 'closed') * 100, 2) }}%

New closed: {{ number_format(floatval($new['c']), 2) }}
