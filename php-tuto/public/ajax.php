<?php

header('Content-Type: application/json');

$result = [];

$result['success'] = password_verify('Chiyome---'.$_POST['password'], '$2y$10$/GSnAqa2MV6IjQUNmOBzNOXjvr1m52.ujf6B1OMBA.ninDzrMapwS');
$result['password'] = $_POST['password'];

echo json_encode($result);
