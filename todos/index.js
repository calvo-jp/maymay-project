/**
 * @typedef Todo
 * @property id {string}
 * @property title {string}
 * @property completed_at {string}
 * @property user {int}
 * @property created_at {string}
 */

/**
 * @param {Todo} todo
 */
function createTodoCard(todo) {
  const template = `
<div class="card">
  <button 
    tabindex="-1" 
    class="card-icon"
    data-id="${todo.id}"
    data-action="__COMPLETE_TODO__"
    data-complete="${!!todo.completed_at}"
  >
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
      <path
        fill-rule="evenodd"
        clip-rule="evenodd" 
        d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"
      />
    </svg>
  </button>

  <div class="card-main">
    <h2 class="card-heading">${todo.title}</h2>
    <p class="card-body">${todo.description}</p>
  </div>

  <div class="card-actions">
    <a 
      href="/todo/todos/edit.php?id=${todo.id}" 
      class="text-primary500"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
        />
      </svg>
    </a>

    <button
      title="delete"
      class="text-danger500"
      data-action="__DELETE_TODO__"
      data-id="${todo.id}"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
      >
        <path 
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
        />
      </svg>
    </button>
  </div>
</div>
`;

  const div = document.createElement("div");
  div.innerHTML = template;
  return div;
}

function fetchTodos() {
  const container = document.getElementById("cards");

  fetch("http://localhost/todo/api/todos.php")
    .then((response) => response.json())
    .then((o) => {
      console.log(o);
      if (o.success) {
        /**
         * @type {Todo[]}
         */
        const todos = o.data;

        todos.forEach(function (todo) {
          container.appendChild(createTodoCard(todo));
        });
      }
    })
    .catch((e) => {
      const msg = e instanceof Error ? e.message : "Something went wrong";
      alert(msg);
    });
}

function handleDeletes() {
  const container = document.getElementById("cards");

  document.addEventListener("click", (e) => {
    if (e.target && e.target.tagName === "BUTTON") {
      /** @type {HTMLButtonElement} */
      const button = e.target;

      if (
        button.hasAttribute("data-id") &&
        button.hasAttribute("data-action") &&
        button.getAttribute("data-action") === "__DELETE_TODO__"
      ) {
        if (!confirm("Are you sure you want to delete this todo?")) {
          return;
        }

        const id = button.getAttribute("data-id");
        const parent0 = button.parentElement; /* .card-actions */
        const parent1 = parent0.parentElement; /* .card */
        const parent2 = parent1.parentElement; /* div */

        fetch(`http://localhost/todo/api/todos.php?id=${id}`, {
          method: "delete",
        })
          .then((res) => {
            if (res.ok) {
              container.removeChild(parent2);
              alert("Successfully deleted todo!");
            } else {
              alert("Something went wrong.");
            }
          })
          .catch((e) => {
            const msg = e instanceof Error ? e.message : "Something went wrong";
            alert(msg);
          });
      }
    }
  });
}

function handleComplete() {
  const container = document.getElementById("cards");

  document.addEventListener("click", (e) => {
    if (e.target && e.target.tagName === "BUTTON") {
      /** @type {HTMLButtonElement} */
      const button = e.target;

      if (
        button.hasAttribute("data-id") &&
        button.hasAttribute("data-action") &&
        button.getAttribute("data-action") === "__COMPLETE_TODO__"
      ) {
        /** already completed */
        if (button.dataset.complete === "true") return;

        if (!confirm("Are you sure you want to mark this todo as completed?")) return;

        const id = button.getAttribute("data-id");

        fetch(`http://localhost/todo/api/todos.php?id=${id}`, {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json",
          },
        })
          .then((res) => res.json())
          .then((obj) => {
            if (obj.success) {
              button.dataset.complete = "true";
            } else {
              alert("Something went wrong.");
            }
          })
          .catch((error) => {
            console.error(error);
          });
      }
    }
  });
}

function handleLogout() {
  /** @type {HTMLButtonElement} */
  const logoutButton = document.querySelector("#logout-button");

  logoutButton.addEventListener("click", () => {
    fetch("http://localhost/todo/api/sessions.php", {
      method: "delete",
    })
      .then((data) => {
        console.log({
          data,
        });

        window.location.href = "/todo/login";
      })
      .catch((error) => {
        console.error(error);
      });
  });
}
