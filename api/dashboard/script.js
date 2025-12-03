const table = "<?= $table ?>";
const api = "<?= $apiUrl ?>";

/* CREATE */
function createRow() {
  let formData = {};
  new FormData(document.querySelector("#createForm")).forEach(
    (v, k) => (formData[k] = v)
  );

  fetch(`${api}?table=${table}`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  }).then(() => location.reload());
}

/* UPDATE */
function editRow(id) {
  fetch(`${api}?table=${table}&id=${id}`)
    .then((r) => r.json())
    .then((data) => {
      let html = `<div id='editBox' class='box'>
                <h3>Edit row #${id}</h3>
                <form id='editForm'>`;

      for (let key in data) {
        if (key === "id") continue;
        html += `${key}<br><input name='${key}' value='${data[key]}'><br><br>`;
      }

      html += `
                </form>
                <button class='btn btn-save' onclick='saveEdit(${id})'>Save</button>
                <button class='btn' onclick='document.getElementById("editBox").remove()'>Cancel</button>
            </div>`;

      document.body.insertAdjacentHTML("beforeend", html);
    });
}

function saveEdit(id) {
  let formData = {};
  new FormData(document.querySelector("#editForm")).forEach(
    (v, k) => (formData[k] = v)
  );

  fetch(`${api}?table=${table}&id=${id}`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((r) => r.json())
    .then(() => location.reload());
}

/* DELETE */
function deleteRow(id) {
  if (!confirm("Really delete?")) return;

  fetch(`${api}?table=${table}&id=${id}`, {
    method: "DELETE",
  })
    .then((r) => r.json())
    .then(() => document.querySelector("#row-" + id).remove());
}
