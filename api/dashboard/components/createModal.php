<!-- Create Modal -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <span class="close" data-close="createModal">&times;</span>
        <h3>Create new row</h3>

        <?php
        $fkMap = [
            'game_id'      => ['table' => 'public_games', 'label' => 'name'],
            'category_id'  => ['table' => 'game_categories', 'label' => 'name'],
            'platform_id'  => ['table' => 'game_platforms', 'label' => 'name'],
            'user_id'      => ['table' => 'public_users', 'label' => 'username'],
            'studio_id'    => ['table' => 'public_studios', 'label' => 'name'],
            'role_id'      => ['table' => 'hrbac_roles', 'label' => 'name'],
            'permission_id' => ['table' => 'hrbac_permissions', 'label' => 'name'],
        ];
        ?>

        <form id="createForm">
            <?php foreach ($columns as $col): ?>
                <?php if ($col === "id") continue; ?>
                <label><b><?= ucfirst(str_replace('_id', '', $col)) ?></b></label><br>

                <?php if (isset($fkMap[$col])): ?>
                    <select name="<?= $col ?>" style="width:300px;">
                        <?php
                        $ref = $fkMap[$col];
                        $items = $pdo->query("SELECT id, {$ref['label']} AS text FROM {$ref['table']} ORDER BY text")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($items as $item) {
                            echo "<option value='{$item['id']}'>{$item['text']}</option>";
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