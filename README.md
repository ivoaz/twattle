README
======

What is TweetEat?
-----------------

TweetEat allows you to research the sentiment for brand or product based on a tweets from http://twitter.com.
It is an academic project created by four Computer Science students in Web Science seminar at the University of Latvia.
The project is used as a TweetEat Battle site at http://tweeteat.tk.

Requirements
------------

TweetEat requires PHP 5.3 or newer, MySQL and MongoDB.


Installation
------------

Clone the project

    git clone git@github.com:ivoaz/tweeteat.git

Clone submodules

    git submodule init
    git submodule update

Copy the distributed config file and configure the databases

    cp app/config/config.ini.dist app/config/config.ini


Collecting tweets
-----------------

Add some objects to the database

    from = new Date()
    till = new Date()
    till.setDay(till.getDay()+7)
    db.objects.insert({
        "_id": "ipad",
        "name": "iPad",
        "keywords": ["ipad"],
        "topical_from": from,
        "topical_till": till
    })

Run the collector service

    php cli/collect.php


Processing collected tweets
------------------

Processing tweets includes several operations - text normalization, object determination, language detection and sentiment analysis based on simple keywords method and Naive Bayesian method.

Before processing, make sure you have a lexicon in your mongodb database and statisical data for Naive Bayesian in your mysql database.

You can import default lexicon by running import script

    php cli/import_lexicon.php

To start processing, run the processor script

    php cli/process.php


Viewing statistics in console
------------------

Run the statistics script

    php cli/stats.php


Viewing battle page in a browser
--------------------------------

Configure your http server and open web/index.php in the browser.
