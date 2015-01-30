<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Test PHP exif</title>
</head>

<body>

<?php
echo "IR_0667.jpg:<br />\n";
$exif = exif_read_data('IR_0667.jpg', 'IFD0');
echo $exif===false ? "No header data found.<br />\n" : "Image contains headers<br />\n";

$exif = exif_read_data('IR_0667.jpg', 0, true);
echo "IR_0667.jpg:<br />\n";
foreach ($exif as $key => $section) {
    foreach ($section as $name => $val) {
        echo "$key.$name: $val<br />\n";
    }
}
?> 
</body>
</html>