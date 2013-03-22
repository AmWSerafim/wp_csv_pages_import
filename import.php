<?php
/* MYSQL CONNECT SETTINGS */
$hostname = "localhost";
$username = "USER_NAME";
$userpass = "USER_PASS";
$dbname = "DB_NAME";

/* CSV FILE LOCATION */
$filepath = "PATH_TO_CSV_FILE";

/* SITE URL */
$site_url = "PUBLISHED_SITE_URL";

function importData($hostname, $username, $userpass, $dbname, $filepath, $site_url){

    $link = mysql_connect($hostname, $username, $userpass) or die ("Can't connect to MySQL server!");
    mysql_select_db($dbname);

    $handle = fopen($filepath, 'r');
    $csv_data_array = array();
    $count = 0;

    while($row = fgetcsv($handle, 0, ',', '"', '\\')){
        $csv_data_array[$count] = $row;
        if($row[2] == ""){
            unset($csv_data_array[$count]);
        }
        $count++;
    }

    unset($csv_data_array[0]);

    foreach ($csv_data_array as $item) {
        
        $addition_content = '';
/*
 * text before content from CSV file
 */        
        $addition_content .= '<h1>'.$item[2].'</h1>';
/*
 *content from CSV file
 */
        $addition_content .= $item[5];
/*
 * text after content from CSV file
 */
        $addition_content .= '
        <p>&nbsp;</p>
        <ul>
            <li>Your financial well being is our #1 priority.</li>
            <p>&nbsp;</p>
            <li>We take a personal interest in the things that are important to you.</li>
            <p>&nbsp;</p>
            <li>U.S. Wealth Management will work with you to gain a clear, complete understanding of your entire financial plan.</li>
        </ul>
        <p>&nbsp;</p>
        ';

        $addition_content .= '
    <div class="textwidget">
        <div class="middle_button">
            <a href="'.$site_url.'category/meet-the-people/" target="_blank" class="middle_btn">Meet Our Boston Team</a>
        </div>
    </div>
    <div style="clear:both;">&nbsp;</div>
    <ul class="middle-box">
        <li id="text-17" class="widget widget_text">
            <div class="textwidget">
                <div class="box-inner">
                    <h2><a href="http://makingcentsjnap.blogspot.com/" target="_blank">Blog</a></h2>
                    <p>Insights from our CEO, John Napolitano, that could influence your financial future.</p>
                    <p><a href="http://makingcentsjnap.blogspot.com/" target="_blank"><img src="'.$site_url.'wp-content/uploads/2013/03/blog7.jpg" width="122" height="100" title="'.$item[6].'"></a>
                </div>
            </div>
        </li>
        <li id="text-18" class="widget widget_text">
            <div class="textwidget">
                <div class="box-inner">
                    <h2><a href="http://visitor.r20.constantcontact.com/manage/optin/ea?v=0017tTKAwTMFkDypDETOmyqOc0WtqvAzuLuUCxSonjrAIlzwUBa7-9WDQGRtTRLvxjvjH936gPx9Okuo6GqF4kZxg%3D%3D" target="_blank">Newsletter</a></h2>
                    <p>Sign up to receive the U.S. Wealth Management company newsletter.</p>
                    <p><a href="http://visitor.r20.constantcontact.com/manage/optin/ea?v=0017tTKAwTMFkDypDETOmyqOc0WtqvAzuLuUCxSonjrAIlzwUBa7-9WDQGRtTRLvxjvjH936gPx9Okuo6GqF4kZxg%3D%3D" target="_blank"><img src="'.$site_url.'wp-content/uploads/2013/03/newsletter.jpeg" width="145" height="100" title="'.$item[7].'"></a>
                </div>
            </div>
        </li>
    </ul>
        ';

        
        $query = "
            INSERT INTO
                wp_posts
            SET
                post_author = 1,
                post_date = '".date("Y-m-d H:i:s")."',
                post_date_gmt = '".date("Y-m-d H:i:s")."',
                post_content = '".mysql_escape_string($addition_content)."',
                post_title = '".mysql_escape_string($item[2])."',
                post_status = 'publish',
                comment_status = 'open',
                ping_status = 'open',
                post_name = '".mysql_escape_string(str_replace(" ", "-", strtolower($item[2])))."',
                post_modified = '".date("Y-m-d H:i:s")."',
                post_modified_gmt = '".date("Y-m-d H:i:s")."',
                post_parent = 0,
                post_type = 'page',
                comment_count = 0
        ";
        //echo $query."<br/>";
        $result = mysql_query($query, $link);
        $post_id = mysql_insert_id();

        $query = "
            UPDATE
                wp_posts
            SET
                guid = '".  mysql_escape_string($site_url."?page_id=".$post_id)."'
            WHERE
                ID = ".$post_id."
        ";
        //echo $query."<br/>";
        $result = mysql_query($query, $link);


        $post_meta_array = array(
            "_edit_last",
            "_edit_lock",
            "_aioseop_description",
            "_aioseop_title",
            "_wp_page_template",
            "_aioseop_keywords"
        );

        $meta_data_array = array(
            1 => "1",
            2 => "1363017486:1",
            3 => mysql_escape_string($item[4]),
            4 => mysql_escape_string($item[2]),
            5 => "landing.php",
            6 => mysql_escape_string(str_replace(" |", ",", $item[3]))
        );

        $counter = 1;
        foreach($post_meta_array as $item){
            $query = "
                INSERT INTO
                    wp_postmeta
                SET
                    post_id = '".$post_id."',
                    meta_key = '".$item."',
                    meta_value = '".$meta_data_array[$counter]."'
            ";
            //echo $query."<br/>";
            $result = mysql_query($query, $link);
            $counter++;
        }
        set_time_limit(100);
        //die('end of one row');
    }
}

if(isset($_GET['run']) && $_GET['run'] = 'true'){
    
    importData($hostname, $username, $userpass, $dbname, $filepath, $site_url);
    
    echo "Import complete";
}

?>