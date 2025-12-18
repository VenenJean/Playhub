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
  })
    .then((r) => {
      if (!r.ok) {
        return r.json().then((errorData) => {
          throw new Error(errorData.error || "Unknown error");
        });
      }
      return r.json();
    })
    .then(() => location.reload())
    .catch((error) => {
      alert(`Error: ${error.message}`);
      console.error(error);
    });
}

/* Edit */
function editRow(id) {
  fetch(`${api}?table=${table}&id=${id}`)
    .then((r) => {
      if (!r.ok) {
        return r.json().then((errorData) => {
          throw new Error(errorData.error || "Unknown error");
        });
      }
      return r.json();
    })
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

        // Normal text field
        else {
          form.innerHTML += `<input type="text" name="${col}" value="${
            data[col] ?? ""
          }" style="width:300px;"><br><br>`;
        }
      });

      // Modal open
      document.querySelector("#editModal").style.display = "block";
      document.querySelector("#editTitle").innerHTML = "Edit row #" + id;

      // Save Button
      document.querySelector("#saveEditBtn").onclick = () => saveEdit(id);
    })
    .catch((error) => {
      alert(`Error: ${error.message}`);
      console.error(error);
    });
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
    .then((r) => {
      if (!r.ok) {
        return r.json().then((errorData) => {
          throw new Error(errorData.error || "Unknown error");
        });
      }
      return r.json();
    })
    .then(() => location.reload())
    .catch((error) => {
      alert(`Error: ${error.message}`);
      console.error(error);
    });
}

/* Delete */
function deleteRow(id) {
  if (!confirm("Really delete?")) return;

  fetch(`${api}?table=${table}&id=${id}`, { method: "DELETE" })
    .then((r) => {
      if (!r.ok) {
        return r.json().then((errorData) => {
          throw new Error(errorData.error || "Unknown error");
        });
      }
      return r.json();
    })
    .then(() => document.getElementById("row-" + id).remove())
    .catch((error) => {
      alert(`Error: ${error.message}`);
      console.error(error);
    });
}
