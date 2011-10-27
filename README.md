README
======

What is TweetEat?
-----------------

TweetEat allows you to research the sentiment for brand or product.
It is an academic project created by four Computer Science students in
Web Science seminar at University of Latvia.


Requirements
------------

TweetEat requires PHP 5.3 or newer and MongoDB.


Installation
------------

Clone the project

    git clone git@github.com:ivoaz/tweeteat.git

Clone submodules

    git submodule init
    git submodule update

Copy the distributed config file and configure the database

    cp app/config/config.ini.dist app/config/config.ini


Collecting tweets
-----------------

Add some products to the database

    db.products.insert({ "_id": "ipad", "name": "iPad", "keywords": ["ipad"] })

Run the collector service

    php cli/collect.php


Determining object
------------------

Run the object determination script

    php cli/determine_object.php


Determining sentiment
---------------------

Run the sentiment determination script

    php cli/determine_sentiment.php

Viewing statistics
------------------

Run the statistics script
    php cli/stats.php
