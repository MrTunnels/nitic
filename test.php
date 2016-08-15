<?php
$data = json_decode('{"stock":[{"IWFJ":12}]}',true);
echo $data['stock'][0]['IWFJ'];
