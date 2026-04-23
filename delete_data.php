<?php
if ($_POST['file'] && file_exists('data/' . $_POST['file'])) {
    unlink('data/' . $_POST['file']);
    http_response_code(200);
    echo 'DELETED';
} else {
    http_response_code(404);
}
?>