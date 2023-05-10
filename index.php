<?php

session_start();

if (!isset($_SESSION['user']['id'])) {
  header('Location: http://localhost/todo/login');
} else {
  header('Location: http://localhost/todo/todos');
}
