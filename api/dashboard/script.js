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
      document.getElementById("editTitle").innerText = `Edit row #${id}`;
      let form = document.getElementById("editForm");
      form.innerHTML = "";

      for (let key in data) {
        if (key === "id") continue;
        form.innerHTML += `
          <label>${key}</label><br>
          <input name="${key}" value="${data[key]}" style="width:300px"><br><br>
        `;
      }

      document.getElementById("saveEditBtn").onclick = () => saveEdit(id);

      openModal("editModal");
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
