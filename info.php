<?php
$max_upload = min((int)ini_get('post_max_size'), (int)ini_get('upload_max_filesize'));
$max_upload = $max_upload * 1024;
echo $max_upload;
?>