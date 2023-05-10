<?php

require_once '../config/connection.php';

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$input = file_get_contents('php://input');


if ($method === 'POST') {
  $data = json_decode($input, true);

  if (
    !isset($data['email']) ||
    !isset($data['password'])
  ) {
    http_response_code(400);
    echo json_encode([
      'message' => 'Missing email or password'
    ]);

    exit();
  }

  $email = $data['email'];
  $password = password_hash($data['password'], PASSWORD_BCRYPT);

  /* check if email already exists in database */
  $sql = "SELECT id FROM users WHERE email = ?";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_store_result($stmt);

  if (mysqli_stmt_num_rows($stmt) > 0) {
    /* email already exists, return error response */
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Email already exists'
    ]);
  } else {
    $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
    $stmt = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param(
      $stmt,
      "ss",
      $email,
      $password
    );

    mysqli_stmt_execute($stmt);

    $userId = mysqli_insert_id($connection);

    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $email;

    http_response_code(201);

    echo json_encode([
      'success' => true,
      'data' => [
        'id' => $userId,
        'email' => $email,
      ]
    ]);
  }
} else if ($method === 'GET') {
  if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([
      'success' => false,
      'message' => 'Forbidden'
    ]);

    exit();
  }

  $userId = $_SESSION['user_id'];
  $sql = "SELECT id, email FROM users WHERE id = ?";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param($stmt, "i", $userId);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);

  if (mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode([
      'success' => false,
      'message' => 'User not found'
    ]);

    exit();
  }

  $user = mysqli_fetch_assoc($result);

  echo json_encode([
    'success' => true,
    'data' => [
      'id' => $user['id'],
      'email' => $user['email']
    ]
  ]);
} else {
  http_response_code(405);
  header('Allow: GET, POST');
  echo json_encode([
    'success' => false,
    'message' => 'Method not allowed'
  ]);
}
