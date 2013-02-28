<?php

/**
 * @file
 * Simple PHP script using twitteroauth.php to pull in Twitter favourites list
 * through new 1.1 JSON API call, and return an RSS feed suitable for
 * consumption by IFTTT
 */

// Define some constants, as provided by Twitter
define(CONSUMER_KEY, '');
define(CONSUMER_SECRET, '');
$oauth_token = '';
$oauth_token_secret = '';
$screen_name = 'YOUR_SCREEN_NAME_HERE';

// Shouldn't need to change anything below here
require_once('twitteroauth.php');
$lastid = trim(file_get_contents('lastid.txt'));

// Setup the API connection
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
$connection->host = 'https://api.twitter.com/1.1/';

// Basic options
$options = array(
  'screen_name' => $screen_name,
  'include_entities' => 'true',
  'since_id' => $lastid, 
  'count' => 200,
);

// Request list of favorites since the last
$content = $connection->get('favorites/list', $options);
if (!count($content)) {
  unset($options['since_id']);
  $options['count'] = 20;
  $content = $connection->get('favorites/list', $options);
}

// Now spit out RSS feed
header("Content-Type: application/rss+xml");
printf('<?xml version="1.0" encoding="UTF-8"?>%s', "\n");
printf('<rss xmlns:atom="http://www.w3.org/2005/Atom" xmlns:georss="http://www.georss.org/georss" version="2.0" xmlns:twitter="http://api.twitter.com">%s', "\n");
printf('  <channel>%s', "\n");
printf('    <title>Twitter / Favourites from %s</title>%s', $screen_name, "\n");
printf('    <link>http://tranzform.ca/fav2rss/index.php</link>%s', "\n");
printf('    <description>Twitter updates favorited by %s.</description>%s', $screen_name, "\n");
printf('    <language>en-us</language>%s', "\n");
printf('    <ttl>40</ttl>%s', "\n");

foreach ($content as $item) {
  printf('  <item>%s', "\n");
  printf('    <title>%s</title>%s', $item->text, "\n");
  printf('    <description>%s</description>%s', $item->text, "\n");
  printf('    <pubDate>%s</pubDate>%s', $item->created_at, "\n");
  printf('    <guid>http://twitter.com/%s/statuses/%s</guid>%s', $item->user->screen_name, $item->id_str, "\n");
  printf('    <link>http://twitter.com/%s/statuses/%s</link>%s', $item->user->screen_name, $item->id_str, "\n");
  printf('    <twitter:source>%s</twitter:source>%s', htmlentities($item->source), "\n");
  printf('  </item>%s', "\n");
}

printf("  </channel>\n");
printf("</rss>\n");

// Save the most recent ID to start from next time
$lastid = $content[0]->id_str;
file_put_contents('lastid.txt', $lastid);
