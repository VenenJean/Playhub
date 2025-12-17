<!-- Table Selection DropDown -->
<form method="GET">
    <label>Select table: </label>
    <select name="table" onchange="this.form.submit()">
        <?php foreach ($tables as $t): ?>
            <option value="<?= $t ?>" <?= $t == $table ? "selected" : "" ?>><?= $t ?></option>
        <?php endforeach; ?>
    </select>
</form>