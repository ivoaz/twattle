<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$tweetColl = $database->getTweetCollection();
$objectColl = $database->getObjectCollection();

$objects = iterator_to_array($objectColl->findTopical());

$tweets = array();
foreach ($objects as $key => $object) {
    $tweets[$key] = $tweetColl->findLatestContainingObject($object['_id'], 10);
    $stats[$key] = $tweetColl->getObjectStats($object['_id']);
}

?>
<!doctype html>
<html>
    <head>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <link rel="stylesheet" type="text/css" href="css/style2.css" />
    </head>

    <body>
        <div id="like">
            <span class="twitter">
                <a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
            </span>

            <span class="facebook">
                <iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ftweeteat.tk&amp;send=false&amp;layout=standard&amp;width=450&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=80&amp;appId=177853892250056" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:80px;" allowTransparency="true"></iframe>
            </span>
        </div>

        <div id="container">
            <div id="caption">TweetEat Battle</div>
            <?php $first = true; foreach ($objects as $key => $object):  ?>
                <div class="prodContainer" id="<?php echo $first ? 'firstProd': 'secondProd' ?>">
                    <h1><?php echo $object['name']?></h1>

                    <img class="prodImg" src="http://<?php echo urlencode($object['name']) ?>.jpg.to" />

                    <div class="result">
                        <h2><?php echo round($stats[$key]['pos']/$stats[$key]['total']*100, 2)?>%</h2>
                        <span>POSITIVE</span>
                    </div>

                    <div class="more">
                        <h2>more</h2>
                        <div class="stats">
                            <div class="total">Total: <?php echo $stats[$key]['total'] ?></div>
                            <div class="positive">Positive: <?php echo $stats[$key]['pos'] ?></div>
                            <div class="negative">Negative: <?php echo $stats[$key]['neg'] ?> </div>
                            <div class="neutral">Neutral: <?php echo $stats[$key]['total'] - $stats[$key]['pos'] - $stats[$key]['neg']; ?> </div>
                            <div class="stats">Tweeted by "human": <?php echo $stats[$key]['total'] - $stats[$key]['spam'] ?> </div>
                            <div class="stats">Tweeted by "machines": <?php echo $stats[$key]['spam'] ?> </div>
                            <!-- <div class="stats">HOT day: 14.09.2011 </div> -->
                        </div>
                    </div>

                    <ul class="tweetList">
                        <?php foreach($tweets[$key] as $tweet): $rating = $tweet['objects'][0]['sentiment']['rating']; ?>
                            <li class="tweet<?php if ($rating > 0) echo ' good'; elseif ($rating < 0) echo ' bad'; ?>">
                                <?php echo $tweet['original']['text'] ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php $first = false; endforeach ?>
        </div>
    </body>
</html>
