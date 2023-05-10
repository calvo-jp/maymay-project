<?php

require "../config/connection.php";

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

/* LOGIN */
if ($method === 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);

  if (
    !isset($data['email']) ||
    !isset($data['password'])
  ) {
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Missing email or password'
    ]);

    exit();
  }

  $email = $data['email'];
  $password = $data['password'];

  /* check if user exists in the database with the given email and password */
  $sql = "SELECT id, password FROM users WHERE email = ? LIMIT 1";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $userId, $passwordHash);
  mysqli_stmt_fetch($stmt);

  if (!$userId || !password_verify($password, $passwordHash)) {
    /*  user doesn't exist or password is incorrect */
    http_response_code(401);
    echo json_encode([
      'success' => false,
      'message' => 'Invalid email or password'
    ]);
  } else {
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $email;

    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Logged in successfully'
    ]);
  }

  mysqli_close($connection);
  exit();
}

/* LOGOUT */
if ($method === 'DELETE') {
  unset($_SESSION['user_id']);
  unset($_SESSION['user_email']);

  echo json_encode([
    'success' => false,
    'message' => 'Logged out successfully'
  ]);

  mysqli_close($connection);
  exit();
}

/* Method Not Allowed */
http_response_code(405);
echo json_encode([
  'success' => false,
  'message' => 'Invalid method'
]);

mysqli_close($connection);
exit();
