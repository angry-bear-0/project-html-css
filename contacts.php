<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $number = $_POST['phone'];
  $list_id = 3410899;

  require_once('textmagic.php');
  $result = save_phone_number($number, $list_id);

  echo json_encode($result);
}
