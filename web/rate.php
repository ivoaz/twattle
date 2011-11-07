<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script>

var Main = {
	count: 0,
	page : 1,
	limit : 10,
	addTweet : function(tweet){
		$('#tweetlist').append('<div id="'+tweet['_id']+'" class="litweet"><div>'+tweet['original']['text']+'</div><button onclick="Main.rate(1, \''+tweet['_id']+'\')">:)</button><button onclick="Main.rate(-1, \''+tweet['_id']+'\')">:(</button><button onclick="Main.rate(0, \''+tweet['_id']+'\')">0</button></div>');
	},
	
	rate : function(x , id){
		Main.count++;
		$("#counter").html(Main.count);
		$('#'+id).remove();
		$.post("api/mod/sentiment/update.php", { rating: x , id: id});
	}
}

function More(){
	$.get("api/mod/sentiment/find.php", { limit: Main.limit , page: Main.page}, function(data){
										var tweets = data;
										for( var i in tweets ){
											Main.addTweet(tweets[i]);
										}
                                        Main.page++;
									}, "json");
}


More();
</script>
</head>
<body>
<div id='tweetlist'>



</div>
<a href="#" class='more' onclick = "More()">More</a>
<div id="counter">0</div>
<body>
</html>
