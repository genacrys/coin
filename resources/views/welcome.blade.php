<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Genacrys coin view</title>

    </head>
    <body>
        <!-- TradingView Widget BEGIN -->
        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
        <script type="text/javascript">
        new TradingView.widget({
            "width": 1080,
            "height": 630,
            "symbol": "KRAKEN:ETHJPY",
            "interval": "D",
            "timezone": "Asia/Tokyo",
            "theme": "Light",
            "style": "1",
            "locale": "en",
            "toolbar_bg": "#f1f3f6",
            "enable_publishing": false,
            "withdateranges": true,
            "hide_side_toolbar": false,
            "allow_symbol_change": true,
            "watchlist": [
                "KRAKEN:ETHJPY",
                "KRAKEN:XBTJPY",
                "KRAKEN:XRPUSD"
            ],
            "details": true,
            "hotlist": true,
            "calendar": true,
            "news": [
                "stocktwits",
                "headlines"
            ]
        });
        </script>
        <!-- TradingView Widget END -->
    </body>
</html>
