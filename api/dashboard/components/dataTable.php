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
         <tr id="row-<?= $row["id"] ?>">
             <?php foreach ($columns as $col): ?>
                 <td>
                     <?php
                        if (isset($fkLookup[$col])) {
                            $ref = $fkLookup[$col];
                            $q = $pdo->prepare("SELECT {$ref['column']} FROM {$ref['table']} WHERE id=?");
                            $q->execute([$row[$col]]);
                            echo $q->fetchColumn() ?? "Unknown";
                        } else {
                            echo htmlspecialchars($row[$col]);
                        }
                        ?>
                 </td>
                 <!-- Convert special characters into HTML entities e.g. & to &amp; -->
             <?php endforeach; ?>
             <td>
                 <button class="btn btn-edit" onclick="editRow(<?= $row['id'] ?>)">Edit</button>
                 <button class="btn btn-del" onclick="deleteRow(<?= $row['id'] ?>)">Delete</button>
             </td>
         </tr>
     <?php endforeach; ?>
 </table>