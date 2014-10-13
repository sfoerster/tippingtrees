
<?php

require('../includes/common/common.php');

class RSS
{
    
    static function GoogleNews($searchURL='https://news.google.com/news?pz=1&cf=all&ned=us&hl=en&topic=n&output=rss')
    {
        $news = simplexml_load_file($searchURL);
        
        $feeds = array();
        
        $i = 0;
        
        foreach ($news->channel->item as $item) 
        {
            preg_match('@src="([^"]+)"@', $item->description, $match);
            $parts = explode('<font size="-1">', $item->description);
        
            $feeds[$i]['title'] = (string) $item->title;
            $feeds[$i]['link'] = (string) $item->link;
            $feeds[$i]['image'] = isset($match[1]) ? $match[1] : null; //$match[1];
            $feeds[$i]['site_title'] = strip_tags($parts[1]);
            $feeds[$i]['story'] = strip_tags($parts[2]);
        
            $i++;
        }
        
        /*echo '<pre>';
        print_r($feeds);
        echo '</pre>';
        
        [2] => Array
        (
            [title] => Los Alamos Nuclear Lab Under Siege From Wildfire - ABC News
            [link] => http://news.google.com/news/url?sa=t&fd=R&usg=AFQjCNGxBe4YsZArH0kSwEjq_zDm_h-N4A&url=http://abcnews.go.com/Technology/wireStory?id%3D13951623
            [image] => http://nt2.ggpht.com/news/tbn/OhH43xORRwiW1M/6.jpg
            [site_title] => ABC News
            [story] => A wildfire burning near the desert birthplace of the atomic bomb advanced on the Los Alamos laboratory and thousands of outdoor drums of plutonium-contaminated waste Tuesday as authorities stepped up ...
        )
        */
        
        return $feeds;
    }
}

?>
