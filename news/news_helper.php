<?php

function updateEducationNews($conn){

    /* CHECK LAST UPDATE */

    $check = $conn->query("
        SELECT created_at
        FROM education_news
        ORDER BY created_at DESC
        LIMIT 1
    ");

    $shouldUpdate = true;

    if($check->num_rows > 0){

        $row = $check->fetch_assoc();

        $lastUpdate = strtotime($row['created_at']);

        /* 3 HOURS CACHE */

        if(time() - $lastUpdate < 10800){

            $shouldUpdate = false;

        }

    }

    if(!$shouldUpdate){
        return;
    }

    /* RSS FEEDS */

    $feeds = [

        "Canada" =>
        "https://news.google.com/rss/search?q=canada+international+students",

        "Australia" =>
        "https://news.google.com/rss/search?q=australia+student+visa",

        "UK" =>
        "https://news.google.com/rss/search?q=uk+international+students",

        "USA" =>
        "https://news.google.com/rss/search?q=usa+student+visa",

        "Scholarships" =>
        "https://news.google.com/rss/search?q=international+scholarships"

    ];

    foreach($feeds as $category => $url){

        $rss = @simplexml_load_file($url);

        if(!$rss){
            continue;
        }

        foreach($rss->channel->item as $item){

            $title =
            $conn->real_escape_string(
                (string)$item->title
            );

            $link =
            $conn->real_escape_string(
                (string)$item->link
            );

            $pubDate = date(
                'Y-m-d H:i:s',
                strtotime((string)$item->pubDate)
            );

            $summary = substr(
                strip_tags((string)$item->description),
                0,
                220
            );

           /* CATEGORY IMAGES */

			$categoryImages = [

				"Canada" =>
				"https://images.unsplash.com/photo-1517935706615-2717063c2225?auto=format&fit=crop&w=800&q=80",

				"Australia" =>
				"https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?auto=format&fit=crop&w=800&q=80",

				"UK" =>
				"https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=800&q=80",

				"USA" =>
				"https://images.unsplash.com/photo-1501594907352-04cda38ebc29?auto=format&fit=crop&w=800&q=80",

				"Scholarships" =>
				"https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80"

			];

			/* FALLBACK */

			$image = $categoryImages[$category]
			?? "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=800&q=80";

            $source = "Google News";

           /* CLEAN DATA */

$title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');

$summary = html_entity_decode($summary, ENT_QUOTES | ENT_HTML5, 'UTF-8');

/* PREPARED STATEMENT */

				$stmt = $conn->prepare("

				INSERT IGNORE INTO education_news
				(
					title,
					summary,
					image_url,
					article_url,
					source_name,
					category,
					published_at
				)

				VALUES
				(
					?, ?, ?, ?, ?, ?, ?
				)

				");

				$stmt->bind_param(
					"sssssss",
					$title,
					$summary,
					$image,
					$link,
					$source,
					$category,
					$pubDate
				);

				$stmt->execute();

				$stmt->close();

        }

    }

}
?>