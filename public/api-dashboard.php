<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>PlayHub API Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0e0e10;
            color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        header {
            background: #202225;
            padding: 15px;
            font-size: 20px;
            text-align: center;
            color: #00c8ff;
        }

        main {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }

        select,
        input,
        button,
        textarea {
            background: #2f3136;
            color: #f2f2f2;
            border: 1px solid #555;
            border-radius: 6px;
            padding: 8px;
        }

        button {
            cursor: pointer;
            background-color: #00c8ff;
            color: #000;
            font-weight: bold;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
            background: #1a1b1e;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #2b2d31;
        }

        tr:hover {
            background: #2f3136;
        }

        #editor {
            margin-top: 20px;
            background: #1a1b1e;
            padding: 15px;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <header>⚙️ PlayHub API Dashboard</header>
    <main>
        <label for="table">Tabelle wählen:</label>
        <select id="table">
            <option value="">-- auswählen --</option>
            <option>public_users</option>
            <option>public_games</option>
            <option>public_reviews</option>
            <option>public_users_games</option>
            <option>public_wishlists</option>
            <option>public_studios</option>
            <option>public_publishers_games</option>
            <option>public_developers_games</option>
            <option>game_categories</option>
            <option>game_games_categories</option>
            <option>game_platforms</option>
            <option>game_games_platforms</option>
            <option>hrbac_roles</option>
            <option>hrbac_permissions</option>
            <option>hrbac_users_roles</option>
            <option>hrbac_roles_inherits</option>
            <option>hrbac_roles_permissions</option>
        </select>

        <button id="load">📥 Laden</button>
        <div id="output"></div>

        <div id="editor" style="display:none;">
            <h3 id="edit-title">Neuen Datensatz erstellen</h3>
            <textarea id="json-input" rows="8" cols="80" placeholder='{"spalte": "wert"}'></textarea><br><br>
            <button id="save">💾 Speichern</button>
            <button id="cancel">❌ Abbrechen</button>
        </div>
    </main>

    <script>
        const apiUrl = "/PlayHub/database/api.php";
        const tableSelect = document.getElementById("table");
        const output = document.getElementById("output");
        const editor = document.getElementById("editor");
        const jsonInput = document.getElementById("json-input");
        const editTitle = document.getElementById("edit-title");
        let currentTable = "";
        let editingId = null;

        document.getElementById("load").addEventListener("click", () => {
            const table = tableSelect.value;
            if (!table) return alert("Bitte Tabelle wählen!");
            currentTable = table;
            loadData(table);
        });

        function loadData(table) {
            fetch(`${apiUrl}?table=${table}`)
                .then(res => res.json())
                .then(data => renderTable(table, data))
                .catch(err => {
                    output.innerHTML = `<p style="color:red;">Fehler: ${err}</p>`;
                });
        }

        function renderTable(table, data) {
            if (!Array.isArray(data)) {
                output.innerHTML = `<p>Keine Daten vorhanden oder Fehler.</p>`;
                return;
            }
            let html = `<button onclick="showEditor()">➕ Neu hinzufügen</button><table><tr>`;
            if (data.length > 0) {
                Object.keys(data[0]).forEach(key => html += `<th>${key}</th>`);
                html += `<th>Aktionen</th></tr>`;
                data.forEach(row => {
                    const id = row.id; // PK überall id
                    html += `<tr>${Object.values(row).map(v => `<td>${v ?? ""}</td>`).join("")}
                             <td>
                                <button onclick="editRow('${table}','${id}')">✏️</button>
                                <button onclick="deleteRow('${table}','${id}')">🗑️</button>
                             </td></tr>`;
                });
            } else html += `<th>Keine Daten gefunden</th></tr>`;
            html += `</table>`;
            output.innerHTML = html;
        }

        function showEditor(data = null) {
            editor.style.display = "block";
            editingId = data ? data.id : null;

            editTitle.textContent = editingId ?
                `Datensatz bearbeiten (ID: ${editingId})` :
                "Neuen Datensatz erstellen";

            if (!data) {
                let defaultData = {};
                if (currentTable === 'public_users') defaultData = {
                    username: "",
                    email: "",
                    password: "",
                    balance: 0
                };
                if (currentTable === 'public_games') defaultData = {
                    name: "",
                    description: "",
                    price: 0
                };
                jsonInput.value = JSON.stringify(defaultData, null, 4);
            } else {
                jsonInput.value = JSON.stringify(data, null, 4);
            }
        }


        function hideEditor() {
            editor.style.display = "none";
            jsonInput.value = "";
        }
        document.getElementById("cancel").addEventListener("click", hideEditor);

        document.getElementById("save").addEventListener("click", () => {
            let jsonData;
            try {
                jsonData = JSON.parse(jsonInput.value);
            } catch {
                alert("Ungültiges JSON!");
                return;
            }

            const method = editingId ? "PUT" : "POST";
            const url = editingId ? `${apiUrl}?table=${currentTable}&id=${editingId}` : `${apiUrl}?table=${currentTable}`;

            fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(jsonData)
            }).then(() => loadData(currentTable));

            hideEditor();
        });

        function editRow(table, id) {
            fetch(`${apiUrl}?table=${table}&id=${id}`)
                .then(res => res.json())
                .then(data => showEditor({
                    ...data,
                    id
                }));
        }

        function deleteRow(table, id) {
            if (!confirm("Wirklich löschen?")) return;
            fetch(`${apiUrl}?table=${table}&id=${id}`, {
                    method: "DELETE"
                })
                .then(() => loadData(table));
        }
    </script>
</body>

</html>