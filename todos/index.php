<?php

session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: http://localhost/todo/login');
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Todo App</title>

  <!-- FONTS -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Shared CSS -->
  <link rel="stylesheet" href="../styles/vars.css" />
  <link rel="stylesheet" href="../styles/reset.css" />
  <link rel="stylesheet" href="../styles/borders.css" />
  <link rel="stylesheet" href="../styles/colors.css" />
  <link rel="stylesheet" href="../styles/forms.css" />
  <link rel="stylesheet" href="../styles/margins.css" />
  <link rel="stylesheet" href="../styles/paddings.css" />
  <link rel="stylesheet" href="../styles/utils.css" />

  <!-- Scoped CSS -->
  <link rel="stylesheet" href="./index.css" />
</head>

<body>
  <div>
    <header class="navbar">
      <h2>Logo</h2>

      <div class="menu">
        <p>Hi, <?php echo explode("@", $_SESSION['user_email'])[0]; ?></p>

        <div class="divider"></div>
        <button type="button" id="logout-button">Logout</button>
      </div>
    </header>

    <div class="container">
      <div id="cards" class="cards"></div>
    </div>

    <a href="/todo/todos/new.php" class="button fab">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
      </svg>
    </a>

    <script src="./index.js"></script>
    <script>
      (function() {
        fetchTodos();
        handleDeletes();
        handleComplete();
        handleLogout();
      })();
    </script>
</body>

</html>