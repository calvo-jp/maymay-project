<?php
session_start();

if (isset($_SESSION['user_id'])) {
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

  <title>Todo App | Login</title>

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
  <div class="container">
    <div class="form-wrapper">
      <h1 class="heading">Log in</h1>

      <form id="form" class="mt-8">
        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input class="form-control" id="email" placeholder="Enter your email" autofocus />
          <div class="form-error">Malformed email address</div>
        </div>

        <div class="form-group mt-4">
          <label class="form-label" for="password">Password</label>
          <input class="form-control" id="password" type="password" placeholder="Enter your password" />
          <div class="form-error">Password must be 8 - 50 characters</div>
        </div>

        <div class="mt-6">
          <button class="button" id="submit-btn">Log in</button>
        </div>
      </form>

      <div class="flex gap-1 mt-6 justify-center">
        <p class="text-neutral500">No account yet?</p>
        <a class="link" href="/todo/signup">Sign up</a>
      </div>
    </div>
  </div>

  <script src="../scripts/validators.js"></script>
  <script>
    (function() {
      /** @type {HTMLFormElement} */
      const loginForm = document.querySelector("#form");
      /** @type {HTMLInputElement} */
      const emailInput = document.querySelector("#email");
      /** @type {HTMLInputElement} */
      const passwordInput = document.querySelector("#password");
      /** @type {HTMLButtonElement} */
      const submitButton = document.querySelector("#submit-btn");

      [emailInput, passwordInput].forEach(field => {
        field.addEventListener("input", function() {
          if (this.hasAttribute("aria-invalid")) {
            this.removeAttribute("aria-invalid");
          }
        })
      });

      let shouldProceed = true;

      loginForm.addEventListener("submit", function(e) {
        e.preventDefault();

        const email = emailInput.value;
        const password = passwordInput.value;

        shouldProceed = true

        if (password.trim().length < 8) {
          passwordInput.setAttribute("aria-invalid", true);
          passwordInput.focus();
          shouldProceed = false;
        }

        if (!isEmail(email)) {
          emailInput.setAttribute("aria-invalid", true);
          emailInput.focus();
          shouldProceed = false;
        }

        if (shouldProceed) {
          const submitButtonOriginalLabel = submitButton.innerText;

          fetch("http://localhost/todo/api/sessions.php", {
              method: 'post',
              body: JSON.stringify({
                email,
                password
              }),
              headers: {
                'Content-Type': 'application/json'
              }
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                window.location.href = "/todo/todos"
              } else {
                alert(data.message)
              }
            })
            .catch((error) => {
              const message = error instanceof Error ? error.message : 'Something went wrong.'
              alert(message)
            })
            .finally(() => {
              submitButton.removeAttribute("disabled");
              submitButton.innerText = submitButtonOriginalLabel;
            })
        }
      });
    })();
  </script>
</body>

</html>