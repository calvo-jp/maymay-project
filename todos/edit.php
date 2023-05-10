<?php

session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: http://localhost/todo/login');
  exit();
}

if (!isset($_GET['id'])) {
  header('Location: http://localhost/todo/todos');
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Todo App | Signup</title>

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
  <link rel="stylesheet" href="./edit.css" />
</head>

<body>
  <div class="container">
    <div class="navbar">
      <a href="/todo/todos">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
        <span>
          Go back
        </span>
      </a>
    </div>

    <div class="inner-wrapper">
      <div class="form-wrapper">
        <h1 class="heading">Edit Todo</h1>

        <form id="form" class="mt-8">
          <div class="form-group mt-4">
            <label class="form-label" for="title">Title</label>
            <input class="form-control" id="title" placeholder="eg. Go to market" autofocus />
            <div class="form-error">Must be 2 or more characters</div>
          </div>

          <div class="form-group mt-4">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" id="description" placeholder="eg. Buy milk"></textarea>
            <div class="form-error">Must be 10 or more characteres</div>
          </div>

          <div class="mt-6">
            <button id="submit-btn" disabled class="button">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="../scripts/autosize.js"></script>
  <script>
    (() => {
      /** @type {HTMLFormElement} */
      const todoForm = document.querySelector('form');
      /** @type {HTMLInputElement} */
      const titleInput = document.querySelector("#title");
      /** @type {HTMLTextAreaElement} */
      const descriptionInput = document.querySelector("#description");
      /** @type {HTMLButtonElement} */
      const submitButton = document.querySelector("#submit-btn");

      autosize(descriptionInput);

      const url = window.location.search;
      const params = new URLSearchParams(url);
      const todoId = params.get('id');

      /* 
       * fetch todo details
       */
      fetch(`http://localhost/todo/api/todos.php?id=${todoId}`)
        .then(res => res.json())
        .then(obj => {
          if (obj.success) {
            const todo = obj.data

            titleInput.value = todo.title
            descriptionInput.value = todo.description
          } else {
            window.location.href = "/todo/todos"
          }
        }).catch(console.error).finally(() => {
          submitButton.removeAttribute('disabled')
        });

      const fields = [titleInput, descriptionInput]

      fields.forEach((field) => {
        field.addEventListener('input', function() {
          if (this.hasAttribute('aria-invalid')) {
            this.removeAttribute('aria-invalid')
          }
        })
      });

      let shouldProceed = true

      todoForm.addEventListener('submit', (e) => {
        e.preventDefault()

        shouldProceed = true

        const title = titleInput.value
        const description = descriptionInput.value

        if (description.trim().length <= 2) {
          descriptionInput.setAttribute('aria-invalid', true)
          descriptionInput.focus()
          shouldProceed = false
        }

        if (title.trim().length <= 2) {
          titleInput.setAttribute('aria-invalid', true)
          titleInput.focus()
          shouldProceed = false
        }

        if (shouldProceed) {
          fetch(`http://localhost/todo/api/todos.php?id=${todoId}`, {
              method: 'PUT',
              body: JSON.stringify({
                title,
                description,
              }),
              headers: {
                'Content-Type': 'application/json'
              }
            })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                alert('Successfully updated todo!')
                window.location.href = "/todo/todos"
              } else {
                alert(data.message)
              }
            })
            .catch(e => {
              const message = (e instanceof Error) ? e.message : 'Something went wrong.'
              alert(e)
            })
        }
      })
    })()
  </script>
</body>


</html>