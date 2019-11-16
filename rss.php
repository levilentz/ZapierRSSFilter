<?php
    // the below lines are a very insecure way of securing this application. We
    // use it as a way to stop crawlers from hitting this link and burning
    // zapier credits
    $appId = "APPID";
    $suppliedAppId = $_GET['app_id'];
    if (isset($suppliedAppId)) {
      if ($appId <> $suppliedAppId) {
        exit();
      }
    } else {
      exit();
    }
    //limit specifies the number of posts to show in the feed. Default 7
    // days limits the number of days before the run time of the rss php app
    // to be run. Default 7
    $limit = $_GET['limit'];
    $days = $_GET['days'];

    if (!isset($limit)) {
      $limit = 5;
    }

    if (!isset($days)) {
      $days = 7;
    }

    // this allows you to specify the website in the get response
    // recommend putting a default to enable default operation
    $suppliedWebsite = $_GET['rss_feed'];
    if (!isset($suppliedWebsite)) {
      $suppliedWebsite = 'DEFAULTWEBSITE';
    }

    $rss = new DOMDocument();
    $rss->load($suppliedWebsite);
    $feed = array();

    $cnt = 0;
    // This is the header of the new rss feed. This strips out the zapier
    // version, correctly point back to your website.
    $headXML=<<<XML
    <rss version="2.0">
    <channel>
    <title>Science of Connectedness</title>
    <link>https://www.scienceofconnectedness.com</link>
    <description>
    Science of Connectedness RSS Instagram Feed
    </description>
    </channel>
    </rss>
    XML;
    $xml=new SimpleXMLElement($headXML);

    $previousDays = strtotime('-'.$days.' days');

    // loop over each 'item' in the rss feed and keep those that have a 'pubDate'
    // greater than $previousDays
    foreach ($rss->getElementsByTagName('item') as $node) {
        if (strtotime($node->getElementsByTagName('pubDate')->item(0)->nodeValue) >= $previousDays ) {
          $item = $xml->channel->addChild('item', '');
          foreach($node->childNodes as $childNode) {
            $item->addChild($childNode->nodeName, htmlspecialchars($childNode->nodeValue));
          }
        }

        $cnt++;

        if ($cnt >= $limit) {
          break;
        }
    }
    // display the xml
    echo $xml->asXML();

?>
