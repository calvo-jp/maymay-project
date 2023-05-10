<?php

require_once '../config/connection.php';

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$input = file_get_contents('php://input');


/* 
 * Check if user is authenticated
 */
if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  echo json_encode([
    'success' => false,
    'message' => 'Forbidden'
  ]);

  exit();
}

/* 
 * Login user's ID
 */
$user_id = $_SESSION['user_id'];


/* CREATE */
if ($method === 'POST') {
  $data = json_decode($input, true);
  $sql = "INSERT INTO todos (title, description, user) VALUES (?, ?, ?)";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param(
    $stmt,
    "ssi",
    $data['title'],
    $data['description'],
    $user_id
  );

  mysqli_stmt_execute($stmt);

  $todo_id = mysqli_insert_id($connection);
  $sql = "SELECT * FROM todos WHERE id = ?";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param($stmt, "i", $todo_id);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);
  $todo = mysqli_fetch_assoc($result);

  http_response_code(201);
  echo json_encode([
    'success' => true,
    'data' => $todo
  ]);
  mysqli_close($connection);
  exit();
}

/* READ (Single) */
if ($method === 'GET' && isset($_GET['id'])) {
  $sql = "SELECT * FROM todos WHERE id = ? AND user = ?";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $_GET['id'],
    $user_id
  );

  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);
  $todo = mysqli_fetch_assoc($result);

  echo json_encode([
    'success' => true,
    'data' => $todo
  ]);
  mysqli_close($connection);
  exit();
}

/* READ (All) */
if ($method === 'GET') {
  $sql = "SELECT * FROM todos WHERE user = ? ORDER BY id DESC";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param($stmt, "i", $user_id);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);
  $todos = mysqli_fetch_all($result, MYSQLI_ASSOC);

  echo json_encode([
    'success' => true,
    'data' => $todos
  ]);
  mysqli_close($connection);
  exit();
}


/* CREATE */
if ($method === 'POST') {
  $data = json_decode($input, true);
  $sql = "INSERT INTO todos (title, description, user) VALUES (?, ?, ?)";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param(
    $stmt,
    "ssi",
    $data['title'],
    $data['description'],
    $user_id
  );

  mysqli_stmt_execute($stmt);

  $todo_id = mysqli_insert_id($connection);
  $sql = "SELECT * FROM todos WHERE id = ?";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param($stmt, "i", $todo_id);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);
  $todo = mysqli_fetch_assoc($result);

  http_response_code(201);
  echo json_encode([
    'success' => true,
    'data' => $todo
  ]);
  mysqli_close($connection);
  exit();
}

/* UPDATE */
if ($method === 'PUT' && isset($_GET['id'])) {
  $data = json_decode($input, true);
  $sql = "UPDATE todos SET title = ?, description = ?, completed_at = ? WHERE id = ?";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param(
    $stmt,
    "sssi",
    $data['title'],
    $data['description'],
    $data['completed_at'],
    $_GET['id']
  );

  mysqli_stmt_execute($stmt);

  $sql = "SELECT * FROM todos WHERE id = ?";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);
  $todo = mysqli_fetch_assoc($result);

  echo json_encode([
    'success' => true,
    'data' => $todo
  ]);
  mysqli_close($connection);
  exit();
}

/* UPDATE (Mark as done) */
if ($method === 'PATCH' && isset($_GET['id'])) {
  $todo_id = $_GET['id'];

  $sql = "UPDATE todos SET completed_at = ? WHERE id = ? AND user = ?";
  $stmt = mysqli_prepare($connection, $sql);

  $completed_at = date('Y-m-d H:i:s');
  mysqli_stmt_bind_param($stmt, "sii", $completed_at, $todo_id, $user_id);
  mysqli_stmt_execute($stmt);

  if (mysqli_stmt_affected_rows($stmt) === 0) {
    http_response_code(404);
    echo json_encode([
      'success' => false,
      'message' => 'Todo not found'
    ]);
    mysqli_close($connection);
    exit();
  }

  $sql = "SELECT * FROM todos WHERE id = ?";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param($stmt, "i", $todo_id);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);
  $todo = mysqli_fetch_assoc($result);

  http_response_code(200);
  echo json_encode([
    'success' => true,
    'data' => $todo
  ]);
  mysqli_close($connection);
  exit();
}


/* DELETE */
if ($method === 'DELETE') {
  $sql = "DELETE FROM todos WHERE id = ?";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
  mysqli_stmt_execute($stmt);

  http_response_code(204);
  mysqli_close($connection);
  exit();
}

http_response_code(405);
echo "Method Not Allowed";
