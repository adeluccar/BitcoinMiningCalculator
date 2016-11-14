<!DOCTYPE html>
<html lang="en" ng-app ng-controller="data">
  <head>
    <title>Bitcoin Mining Calculator</title>
    <meta name="description" content="An easy to use utility used to calculate a Bitcoin miner's potential profits in BTC and multiple fiat
                                currencies. The calculator fetches price and Bitcoin network data from the internet
                                 and only requires the hash rate (speed of mining) from the user. A projected future profit
                                  chart is created dynamically and displayed instantly.">
    <meta name="keywords" content="Bitcoin,Mining,Profitability,Calculator,AngularJS,AJAX,currency,money,profit,finance,cryptocurrency">
    <meta name="author" content="Karl Diab">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.0/angular.min.js"></script>
    <script src="js/app.js"></script>
    <script src="js/Chart.min.js"></script>
    <link rel="stylesheet" href="css/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel='stylesheet' href='../MiningCalcSideBar/style/style.css'>
    <link rel='stylesheet prefetch' href='http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-73629036-1', 'auto');
      ga('send', 'pageview');

    </script>
  </head>

<body> 
    <?php 
    require_once('../MiningCalcSideBar/echoSideBar.php');
    echoTopHalf();
    ?> 
    <div id="header">
        <a href="http://www.karldiab.com"><img align="right" src="images/logo75.png" alt="KarlDiab Logo" id="KDLogo"/></a>
        <h1>Bitcoin Mining Calculator</h1>
    </div>
    <div id="desktopAdBanner">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- BTCCalcBanner -->
        <ins class="adsbygoogle"
            style="display:inline-block;width:728px;height:90px"
            data-ad-client="ca-pub-7592216756911114"
            data-ad-slot="7800082185"></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    <div id="mobileAdBanner">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- BTCBannerMobile -->
        <ins class="adsbygoogle"
            style="display:block"
            data-ad-client="ca-pub-7592216756911114"
            data-ad-slot="6323348984"
            data-ad-format="auto"></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    <div class="row">
        <div class="animated zoomInRight col-md-5">
                <table>
                    <tr>
                        <th>Your Hashrate:</th>
                        <td>
                            <input type="number" id="userHash" ng-model="userHash" 
                            ng-change="computeProfits()" placeholder="Enter Hashrate" />
                            <select ng-model="userHashSuffix" ng-change="computeProfits()">
                            <option value="GH" selected="selected" ng-init="userHashSuffix = 'GH'">GH/s</option>
                            <option value="TH">TH/s</option>
                            <option value="PH">PH/s</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Network Difficulty:</th>
                        <td><input type="number" ng-model="difficulty" ng-change="computeProfits()"/></td>
                    </tr>
                    <tr>
                        <th>BTC Price:</th>
                        <td><input type="number" ng-model="price" ng-change="computeProfits()"/>
                            <select ng-model="currency" ng-change="fetchPriceOnly()">
                            <option value="USD" ng-init="currencyCode = 'USD'">USD</option>
                            <option value="CNY">CNY</option>
                            <option value="CAD">CAD</option>
                            <option value="AUD">AUD</option>
                        </td>
                    </tr>
                    <tr>
                        <th>Block Reward:</th>
                        <td><input type="number" ng-model="reward" ng-change="computeProfits()"/> BTC</td>
                    </tr>
                    <tr>
                        <th>Power Consumption:</th>
                        <td><input type="number" ng-model="wattage" ng-change="computeProfits()" ng-init="wattage = 0"/>
                            <select ng-model="powerSuffix" ng-change="computeProfits()">
                            <option value="W" selected="powerSuffix" ng-init="powerSuffix = 'W'">W</option>
                            <option value="kW">kW</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Power Cost:</th>
                        <td><input type="number" ng-model="powerCost" ng-change="computeProfits()" ng-init="powerCost = 0"/>{{ currency }} / kWh</td>
                    </tr>
                    <tr>
                        <th>Pool Fee</th>
                        <td><input type="number" ng-model="poolFee" ng-change="computeProfits()" ng-init="poolFee = 0"/> %</td>
                    </tr>
                    <tr>
                        <th>Difficulty Increase</th>
                        <td><input type="number" ng-model="nextDifficulty" ng-change="computeProfits()" ng-init="nextDifficulty = 5"/> % / (~2 weeks)</td> 
                    </tr>
                </table>
                <h2>This Difficulty</h2>
            <table>
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>BTC</th>
                        <th>{{currency}}</th>
                        <th>Power Cost ({{currency}})</th>
                        <th>Pool Fees ({{currency}})</th>
                        <th>Profit ({{currency}})</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Hourly</td>
                        <td>{{values[0][0]|number:6}}</td>
                        <td>{{values[1][0]|currency}}</td>
                        <td>{{values[2][0]|currency}}</td>
                        <td>{{values[3][0]|currency}}</td>
                        <td ng-class="{negative: values[4][0] < 0}">{{values[4][0]|currency}}</td>
                    </tr>
                    <tr>
                        <td>Daily</td>
                        <td>{{values[0][1]|number:6}}</td>
                        <td>{{values[1][1]|currency}}</td>
                        <td>{{values[2][1]|currency}}</td>
                        <td>{{values[3][1]|currency}}</td>
                        <td ng-class="{negative: values[4][1] < 0}">{{values[4][1]|currency}}</td>
                    </tr>
                    <tr>
                        <td>Weekly</td>
                        <td>{{values[0][2]|number:6}}</td>
                        <td>{{values[1][2]|currency}}</td>
                        <td>{{values[2][2]|currency}}</td>
                        <td>{{values[3][2]|currency}}</td>
                        <td ng-class="{negative: values[4][2] < 0}">{{values[4][2]|currency}}</td>
                    </tr>
                    <tr>
                        <td>Monthly</td>
                        <td>{{values[0][3]|number:6}}</td>
                        <td>{{values[1][3]|currency}}</td>
                        <td>{{values[2][3]|currency}}</td>
                        <td>{{values[3][3]|currency}}</td>
                        <td ng-class="{negative: values[4][3] < 0}">{{values[4][3]|currency}}</td>
                    <tr>
                        <td>Annually</td>
                        <td>{{values[0][4]|number:6}}</td>
                        <td>{{values[1][4]|currency}}</td>
                        <td>{{values[2][4]|currency}}</td>
                        <td>{{values[3][4]|currency}}</td>
                        <td ng-class="{negative: values[4][4] < 0}">{{values[4][4]|currency}}</td>
                    </tr>
                </tbody>
            </table>
            <h2>Next Difficulty</h2>
            <table>
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>BTC</th>
                        <th>{{currency}}</th>
                        <th>Power Cost ({{currency}})</th>
                        <th>Pool Fees ({{currency}})</th>
                        <th>Profit ({{currency}})</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Hourly</td>
                        <td>{{values[5][0]|number:6}}</td>
                        <td>{{values[6][0]|currency}}</td>
                        <td>{{values[2][0]|currency}}</td>
                        <td>{{values[7][0]|currency}}</td>
                        <td ng-class="{negative: values[8][0] < 0}">{{values[8][0]|currency}}</td>
                    </tr>
                    <tr>
                        <td>Daily</td>
                        <td>{{values[5][1]|number:6}}</td>
                        <td>{{values[6][1]|currency}}</td>
                        <td>{{values[2][1]|currency}}</td>
                        <td>{{values[7][1]|currency}}</td>
                        <td ng-class="{negative: values[8][1] < 0}">{{values[8][1]|currency}}</td>
                    </tr>
                    <tr>
                        <td>Weekly</td>
                        <td>{{values[5][2]|number:6}}</td>
                        <td>{{values[6][2]|currency}}</td>
                        <td>{{values[2][2]|currency}}</td>
                        <td>{{values[7][2]|currency}}</td>
                        <td ng-class="{negative: values[8][2] < 0}">{{values[8][2]|currency}}</td>
                    </tr>
                    <tr>
                        <td>Monthly</td>
                        <td>{{values[5][3]|number:6}}</td>
                        <td>{{values[6][3]|currency}}</td>
                        <td>{{values[2][3]|currency}}</td>
                        <td>{{values[7][3]|currency}}</td>
                        <td ng-class="{negative: values[8][3] < 0}">{{values[8][3]|currency}}</td>
                    </tr>
                    <tr>
                        <td>Annually</td>
                        <td>{{values[5][4]|number:6}}</td>
                        <td>{{values[6][4]|currency}}</td>
                        <td>{{values[2][4]|currency}}</td>
                        <td>{{values[7][4]|currency}}</td>
                        <td ng-class="{negative: values[8][4] < 0}">{{values[8][4]|currency}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-5 animated fadeIn" id="chartContainer">
            <div id="chartNotReady" ng-hide="myLineChart">
                <h3>Enter hashrate data for responsive chart!</h3>
            </div>
            <div  ng-show="myLineChart">
            <h3>Total Profit ({{currency}})</h3>
            <canvas id="myChart" width="300" height="400"></canvas><br/>
            Time Frame:
            <input type="number" ng-model="timeFrame" id="axisChange" ng-init="timeFrame = 12" ng-change="changeAxis()"/> Months
            </div>
        </div>
        <div class="col-md-2">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- BTCSideBanner -->
            <ins class="adsbygoogle"
                    style="display:block"
                    data-ad-client="ca-pub-7592216756911114"
                    data-ad-slot="9276815388"
                    data-ad-format="auto"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </div>
    <div id="authorInfo">
        <strong>Written by Karl Diab using AngularJS and Chart.js. The utility fetches live 
            Bitcoin network & price data from bitcoin.toshi.io and bitcoinaverage.com</strong></br>
        <a href="https://www.youtube.com/watch?v=Um63OQz3bjo"><button class="btn btn-warning btn-sm">What Is Bitcoin?</button></a>
        <a href="http://www.karldiab.com"><button class="btn btn-success btn-sm">Website</button></a>
        <a href="https://github.com/karldiab/BitcoinMiningCalculator"><button class="btn btn-primary btn-sm">Source Code</button></a>
    </div>
    <?php 
    echoBottomHalf();
    ?>
</body>
</html>


