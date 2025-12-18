<!-- Table Data -->
<table>
    <tr>
        <!-- Generates all columns dynamically -->
        <?php foreach ($columns as $col):
            $label = str_replace('_id', '', $col); // game_id -> game
        ?>
            <th><?= ucfirst($label) ?></th>
        <?php endforeach; ?>
        <th>Actions</th>
    </tr>

    <!-- Insert data from columns dynamically as rows -->
    <?php foreach ($rows as $row): ?>
        <tr id="row-<?= htmlspecialchars($row["id"]) ?>">
            <?php foreach ($columns as $col): ?>
                <?php $raw = array_key_exists($col, $row) ? $row[$col] : null; ?>
                <td data-col="<?= htmlspecialchars($col) ?>" data-value="<?= htmlspecialchars($raw) ?>">
                    <?php
                    $label = getFkLabel($col, $raw);
                    if ($label !== null) {
                        echo htmlspecialchars($label);
                    } else {
                        echo htmlspecialchars($raw);
                    }
                    ?>
                </td>
            <?php endforeach; ?>
            <td>
                <button class="btn btn-edit" onclick="editRow(<?= htmlspecialchars(json_encode($row['id'])) ?>)">Edit</button>
                <button class="btn btn-del" onclick="deleteRow(<?= htmlspecialchars(json_encode($row['id'])) ?>)">Delete</button>
            </td>
        </tr>
    <?php endforeach; ?>
</table>