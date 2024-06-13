<?php
header("Refresh: 25");
// Create a SQLite database connection
$conn = new SQLite3('search_history.db');
$conn->busyTimeout(5000); // 5 seconds

// Create a table to store search history
$conn->exec('CREATE TABLE IF NOT EXISTS search_history
             (id INTEGER PRIMARY KEY, url TEXT, status TEXT, last_checked TEXT, timestamp TEXT)');

session_start();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $prompt_text = 'Running.....';
    $url = $_POST['url'];
    $url = str_replace(" ", "", $url);

    
    // Check captcha
    if (!isset($_POST['captcha']) || $_POST['captcha']!= $_SESSION['captcha']) {
        $prompt_text = 'Invalid captcha';
    } else {
        // Check if the string already starts with a protocol (e.g. http://, https://, etc.)
        if (!preg_match('/^https?:\/\//', $url)) {
            // If not, assume it's a URL without a protocol and add http://
            $url = "http://". $url;
        }
        if (substr($url, -7) == '.onion/') {
            $url = substr($url, 0, -1);
        }

        if (substr($url, -6) == '.onion') {
            $start_idx = strpos($url, 'http://') + strlen('http://');
            $end_idx = strpos($url, '.onion');
            $char_count = $end_idx - $start_idx;
            if ($char_count == 56) {


                // set manual status
                $status = 'Pending';
                // Insert search history into database
                $conn->exec("INSERT INTO search_history (url, status, last_checked, timestamp) VALUES ('$url', '$status', datetime('now'), datetime('now'))");
                $prompt_text = 'Ready';
            } else {
                $prompt_text = 'Invalid url';
            }
            
        } else {
            $prompt_text = 'Invalid url';
        }
    }
    
} else {
    $prompt_text = 'Ready';
}

// Generate a new captcha
$captcha = rand(10000, 99999);
$_SESSION['captcha'] = $captcha;

// Create a PNG image for the captcha
$im = imagecreate(60, 16);
$bg = imagecolorallocate($im, 34, 34, 34); // background color
$fg = imagecolorallocate($im, 170, 170, 170); // text color
imagefilledrectangle($im, 0, 0, 50, 20, $bg);
imagestring($im, 5, 10, 5, $captcha, $fg);
imagepng($im, 'captcha.png');
imagedestroy($im);

// Retrieve last 10 search history from database
$result = $conn->query("SELECT * FROM search_history ORDER BY timestamp DESC LIMIT 10");
$search_history = array();
while ($row = $result->fetchArray()) {
    $search_history[] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Is That Onion Down? - Check Onion URL Status</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Check the status of an onion URL. Is it up or down?">
    <meta name="keywords" content="onion url, onion status, is that onion down, dark web, tor">
    <meta name="author" content="Your Name">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://isthatoniondown.com">
    <link rel="icon" type="image/png" href="favicon.png">

    <style>
        body {
            background-color: #333;
            color: #aaa;
            font-family: monospace;
            margin: 0;
            padding: 0;
            height: 100vh; /* added */
            display: flex; /* added */
            flex-direction: column; /* added */
        }
     .terminal {
            padding: 10px;
            border: 1px solid #444;
            border-radius: 10px;
            background-color: #222;
            width: 90%;
            margin: 20px auto;
            flex-grow: 1; /* added */
        }
     .prompt {
            color: #66d9ef;
        }
     .up {
            color: #2ecc71;
        }
     .down {
            color: #e74c3c;
        }
     .status-barr {
            background-color: #444;
            color: #aaa;
            padding: 10px;
            text-align: center;
            width:95%;
            height: 20px; /* adjust the height as needed */
        }
      .status-bari {
            background-color: #444;
            color: #e74c3c;
            padding: 10px;
            text-align: center;
            text-color: #e74c3c;
            width:95%;
            height: 20px; /* adjust the height as needed */
        }
    </style>
</head>
<body>
    <div class="terminal">
        <h1 class="prompt">$ is that onion down?</h1>

        <form action="" method="post">
            <input type="hidden" name="submitted" value="0">
            dig@server:~# curl <input type="text" name="url" placeholder="enter or paste a url" style="background-color: #333; color: #aaa; border: none; padding: 5px; width: 80%">
        <br>    
	dig@server:~#  <img src="captcha.png" alt="captcha" style="width: 50px; height: 20px;"> <input type="text" name="captcha" placeholder=" <-- enter captcha" style="background-color: #333; color: #aaa; border: none; padding: 5px; width: 10%">
            <input type="submit" value="Check" style="background-color: #444; color: #aaa; border: none; padding: 5px; cursor: pointer" <?php if ($prompt_text == 'Running.....') echo 'disabled';?>>
        <br><br>
	<div class="<?php echo $prompt_text == 'Ready'? 'tatus-barr' : 'tatus-bari';?>">
            dig@server:~# status/<?php echo $prompt_text;?>
        </div>
        </form>



        <h2 class="prompt">$ last 10 searches</h2>
        <ul style="list-style: none; padding: 0; margin: 0">
            <?php foreach ($search_history as $row) {?>
                <li style="padding: 5px; border-bottom: 1px solid #333">
                    <span class="prompt">$</span><span class="<?php echo $row[2] == 'UP'? 'up' : 'down';?>"> <?php echo $row[2];?> </span> | [<?php echo $row[1];?>] - [<?php echo $row[3];?>]
                </li>
            <?php }?>
        </ul>

        <p style="text-align: center; margin-top: 20px;">
            <a href="https://github.com/b1nb4s5h/isthatoniondown" target="_blank">Find, follow and star this on Github</a>
        </p>
    </div>
   
</body>
</html>
