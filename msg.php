<?php

$string = get_option("whatsapp_message");
$string = str_replace(array_keys($row), array_values($row), $string);
$string = urlencode($string);
$wa = 'https://web.whatsapp.com/send?phone='.$row["phone"].'&text='.$string;