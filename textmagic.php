<?php

function save_phone_number($number, $list_id)
{
  $username = 'adbeaver';
  $password = 'UIy5lz3aym9uiq2bllBn8DRLQe7txe';
  $endpoint = 'https://rest.textmagic.com/api/v2/lists/' . $list_id . '/contacts';

  // Validate the phone number format
  if (!preg_match('/^\+?[1-9]\d{1,14}$/', $number)) {
    return array("status" => 'error', "message" => 'Invalid format. Please use international format, e.g. +14155552671');
  }

  // Check if the phone number already exists in the list
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $endpoint);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($username . ':' . $password),
  ));
  $response = curl_exec($ch);
  $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($http_status == 200) {
    $contacts = json_decode($response, true)['resources'];
    foreach ($contacts as $contact) {
      if ($contact['phone'] == $number) {
        return array("status" => 'error', "message" => 'Phone number is already on the list.');
      }
    }
  } else {
    return array("status" => 'error', "message" => 'Error retrieving contacts.');
  }

  // Add the phone number to the list
  $data = array(
    'phone' => $number,
    'lists' => $list_id
  );

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://rest.textmagic.com/api/v2/contacts');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($username . ':' . $password),
  ));

  $response = curl_exec($ch);
  $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);

  if ($http_status == 201) {
    return array("status" => 'success', "message" => 'Phone number saved successfully!');
  } else {
    return array("status" => 'error', "message" => 'Error saving phone number.');
  }
}
