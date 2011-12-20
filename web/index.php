<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$tweetColl = $database->getTweetCollection();
$objectColl = $database->getObjectCollection();

$objects = iterator_to_array($objectColl->findTopical());

foreach ($objects as $key => $object) {
    $objects[$key]['tweets'] = $tweetColl->findForBattlePage($object['_id'], 10);
}

?>
<!doctype html>
<html>
    <head>
        <title>TweetEat - The battle of the brands</title>

        <link rel="stylesheet" type="text/css" media="all" href="css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
    </head>

    <body>
        <div class="topbar">
            <div class="topbar-inner">
                <div class="container">
                    <a class="brand" href="/">TweetEat</a>

                    <ul class="nav">
                        <li class="active"><a href="/">Battle</a></li>
                        <li><a href="#about">About</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container battle">
            <h1>The battle of the brands<br /><small>Which will you tweet about?</small></h1>

            <div class="battle-images">
                <?php foreach ($objects as $object): ?>
                    <span class="battle-image">
                        <img class="thumbnail" width="330" src="http://<?php echo htmlentities($object['name']) ?>-logo.jpg.to" alt="<?php echo $object['name'] ?>" />
                    </span>
                <?php endforeach ?>
            </div>

            <div class="battle-stats">
                <?php foreach ($objects as $object): ?>
                    <ul class="battle-sentiment unstyled">
                        <li class="total"><?php echo $object['total_tweets'] ?></li>
                        <li class="positive"><?php echo round($object['positive_tweets']/($object['positive_tweets']+$object['negative_tweets'])*100) ?>% positive tweets</li>
                        <li class="negative"><?php echo round($object['negative_tweets']/($object['positive_tweets']+$object['negative_tweets'])*100) ?>% negative tweets</li>
                        <li class="neutral"><?php echo round(($object['total_tweets']-$object['positive_tweets']-$object['negative_tweets'])/$object['total_tweets']*100) ?>% neutral tweets</li>
                    </ul>
                <?php endforeach ?>
            </div>

            <div class="battle-tweets">
                <?php foreach ($objects as $object): ?>
                    <table class="condensed-table">
                        <thead>
                            <tr>
                                <th><h3>Latest <?php echo htmlentities($object['name']) ?> tweets</h3></th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php foreach ($object['tweets'] as $tweet): $rating = $tweet['sentiment']['rating']; ?>
                                <tr style="background-color: <?php echo $rating > 0 ? '#eeffee' : ($rating < 0 ? '#ffeeee' : '#f8f8f8') ?>;">
                                    <td><?php echo $tweet['original_text'] ?></td>
                                </tr>
                             <?php endforeach ?>
                        </tbody>
                    </table>
                <?php endforeach ?>
            </div>
        </div>

        <div class="container">
            <footer>
                <p>
                    <span class="twitter">
                        <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://tweeteat.tk/" data-lang="en" data-size="large">Tweet</a>
                        <script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                    </span>

                    <span class="facebook">
                        <iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ftweeteat.tk&amp;send=false&amp;layout=standard&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=35&amp;appId=269258999789915" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:26px;" allowTransparency="true"></iframe>
                    </span>
                </p>
                
                <p>&copy; TweetEat 2011</p>
            </footer>
        </div>
    </body>
</html>
