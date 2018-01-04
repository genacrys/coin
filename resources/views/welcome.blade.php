<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Trading chart</title>
    </head>
    <body style="margin: 0px;">
        <div style="height: 100vh;">
            <!-- TradingView Widget BEGIN -->
            <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
            <script type="text/javascript">
            new TradingView.widget({
                "autosize": true,
                "symbol": "BITFLYER:FXBTCJPY",
                "interval": "D",
                "timezone": "Asia/Tokyo",
                "theme": "Light",
                "style": "1",
                "locale": "en",
                "enable_publishing": false,
                "withdateranges": true,
                "hide_side_toolbar": false,
                "allow_symbol_change": true,
                "watchlist": [
                    "BITFLYER:FXBTCJPY",
                    "BITFINEX:BTCUSD",
                    "BITFINEX:ETHUSD",
                    "BITFINEX:BCHUSD",
                    "BITFINEX:LTCUSD",
                    "BITFINEX:XRPUSD",
                    "BITFINEX:XMRUSD",
                    "BITFINEX:IOTUSD",
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
        </div>
    </body>
</html>
