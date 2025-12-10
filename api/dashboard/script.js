function openModal(id) {
  document.getElementById(id).style.display = "block";
}

function closeModal(id) {
  document.getElementById(id).style.display = "none";
}

document.querySelectorAll(".close").forEach((c) => {
  c.onclick = () => closeModal(c.dataset.close);
});

/* Open Create Modal */
document.getElementById("openCreateModal").onclick = () => {
  openModal("createModal");
};

// Close modal when clicking outside of it
window.onclick = function (event) {
  if (event.target.classList.contains("modal")) {
    event.target.style.display = "none";
  }
};

/* Create */
function createRow() {
  let formData = {};
  new FormData(document.querySelector("#createForm")).forEach(
    (v, k) => (formData[k] = v)
  );

  fetch(`${api}?table=${table}`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(formData),
  }).then(() => location.reload());
}

/* Edit */
function editRow(id) {
  fetch(`${api}?table=${table}&id=${id}`)
    .then((r) => r.json())
    .then((data) => {
      const form = document.querySelector("#editForm");
      form.innerHTML = ""; // Reset

      columns.forEach((col) => {
        if (col === "id") return;

        let label = col.replace("_id", "");
        form.innerHTML += `<label><b>${label}</b></label><br>`;

        // FK Dropdown
        if (fkMap[col]) {
          fetch(`${api}?table=${fkMap[col].table}`)
            .then((r) => r.json())
            .then((items) => {
              let html = `<select name="${col}" style="width:300px">`;
              items.forEach((it) => {
                html += `<option value="${it.id}" ${
                  it.id == data[col] ? "selected" : ""
                }>${it[fkMap[col].label]}</option>`;
              });
              html += "</select><br><br>";

              form.insertAdjacentHTML("beforeend", html);
            });
        }

        // Normales Textfeld
        else {
          form.innerHTML += `<input type="text" name="${col}" value="${
            data[col] ?? ""
          }" style="width:300px;"><br><br>`;
        }
      });

      // Modal öffnen
      document.querySelector("#editModal").style.display = "block";
      document.querySelector("#editTitle").innerHTML = "Edit row #" + id;

      // Save Button übernimmt deine vorhandene Funktion!
      document.querySelector("#saveEditBtn").onclick = () => saveEdit(id);
    });
}

function mapHasKey(column) {
  return Object.keys(mapFK).includes(column);
}

const mapFK = {
  game_id: { table: "public_games", column: "name" },
  category_id: { table: "game_categories", column: "name" },
  platform_id: { table: "game_platforms", column: "name" },
  user_id: { table: "public_users", column: "username" },
  studio_id: { table: "public_studios", column: "name" },
  role_id: { table: "hrbac_roles", column: "name" },
  permission_id: { table: "hrbac_permissions", column: "name" },
  parent_role_id: { table: "hrbac_roles", column: "name" },
  child_role_id: { table: "hrbac_roles", column: "name" },
};

function loadFK(column, selected) {
  if (!mapFK[column]) return;

  fetch(`${api}?table=${mapFK[column].table}`)
    .then((r) => r.json())
    .then((data) => {
      let select = document.querySelector(`select[name="${column}"]`);
      data.forEach((e) => {
        let o = document.createElement("option");
        o.value = e.id;
        o.text = e[mapFK[column].column];
        if (e.id == selected) o.selected = true;
        select.appendChild(o);
      });
    });
}

function mapHasKey(column) {
  return mapFK[column] !== undefined;
}

/* Save Edit */
function saveEdit(id) {
  let formData = {};
  new FormData(document.querySelector("#editForm")).forEach(
    (v, k) => (formData[k] = v)
  );

  fetch(`${api}?table=${table}&id=${id}`, {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(formData),
  })
    .then((r) => r.json())
    .then(() => location.reload());
}

/* Delete */
function deleteRow(id) {
  if (!confirm("Really delete?")) return;

  fetch(`${api}?table=${table}&id=${id}`, { method: "DELETE" })
    .then((r) => r.json())
    .then(() => document.getElementById("row-" + id).remove());
}
