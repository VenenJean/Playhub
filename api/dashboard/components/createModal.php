<!-- Create Modal -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <span class="close" data-close="createModal">&times;</span>
        <h3>Create new row</h3>

        <form id="createForm">
            <?php foreach ($columns as $col): ?>
                <?php if ($col === "id") continue; ?>
                <label><b><?= ucfirst(str_replace('_id', '', $col)) ?></b></label><br>

                <?php if (isset($fkMap[$col])): ?>
                    <select name="<?= $col ?>" style="width:300px;">
                        <?php
                        $items = getFkOptions($col);
                        foreach ($items as $item) {
                            echo "<option value='" . htmlspecialchars($item['id']) . "'>" . htmlspecialchars($item['text']) . "</option>";
                        }
                        ?>
                    </select><br><br>
                <?php else: ?>
                    <input type="text" name="<?= $col ?>" style="width:300px"><br><br>
                <?php endif; ?>
            <?php endforeach; ?>
        </form>


        <button class="btn btn-save" onclick="createRow()">Create</button>
    </div>
</div>