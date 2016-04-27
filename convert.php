<?php
include('YnabCsvConverter.php');

$file = $_FILES['file']['tmp_name'];

if(mime_content_type($file) == 'text/plain'){
    $converter = new YnabCsvConverter();
    echo json_encode($converter->convert($_FILES['file']['tmp_name']));
}
