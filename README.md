twitfavs2rss
============

Grab a JSON list of latest Twitter Favorites and emit an RSS feed for consumption by IFTTT.

Setup
-----

1. Create a new application at https://dev.twitter.com/apps/new
2. Copy your Consumer key, Consumer secret, Access token, and Access token secret, and paste into the initial lines of rss.php
3. Clone @abraham's twitteroauth library from https://github.com/abraham/twitteroauth
4. Place OAuth.php and twitteroauth.php in the same directory as rss.php
5. Create a lastid.txt file containing the status ID where you want to begin your feed. This file will be updated dynamically
   each time rss.php runs.
6. Place these 4 files on a public webserver, and point IFTTT's "feed" rule to http://yourwebserver/rss.php
