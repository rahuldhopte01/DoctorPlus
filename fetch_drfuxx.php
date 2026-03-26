<?php
$html = file_get_contents('https://drfuxx.stratolution.de/');
file_put_contents('drfuxx_homepage.html', $html);
echo "done";
