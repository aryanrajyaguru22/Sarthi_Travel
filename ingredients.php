<?php
include 'db.php';
include 'navbar.php';
session_start();

$all_units = ['કિલો', 'ગ્રામ', 'લિટર', 'મિલી લિટર', 'નંગ', 'પેકેટ'];

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $units = $_POST['units'] ?? [];

    if (empty($name) || count($units) === 0) {
        echo "<script>alert('એક વસ્તુ માટે કોઈ એક માપ એકેમ લાહો');</script>";
    } else {
        $conn->query("INSERT INTO ingredients (name) VALUES ('$name')");
        $ingredient_id = $conn->insert_id;

        foreach ($units as $unit) {
            $unit = trim($unit);
            if (in_array($unit, $all_units)) {
                $conn->query("INSERT INTO ingredient_units (ingredient_id, unit) VALUES ($ingredient_id, '$unit')");
            }
        }
        echo "<script>alert('વસ્તુ સામગ્રી સેવ થઇ ગઈ છે '); window.location='ingredients.php';</script>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM ingredients WHERE id=$id");
    echo "<script>window.location='ingredients.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>વસ્તુ સામગ્રી</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        h2, h3 {
            color: #4CAF50;
            text-align: center;
            margin-top: 30px;
        }
        .form-container {
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus {
            border-color: #4CAF50;
        }
        input[type="checkbox"] {
            margin-right: 10px;
        }
        button {
            padding: 12px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #f2f2f2;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            color: #45a049;
        }
        .delete-btn {
            color: #e74c3c;
            cursor: pointer;
            text-decoration: underline;
        }
        .delete-btn:hover {
            color: #c0392b;
        }
        .confirmation-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .popup-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            max-width: 300px;
            width: 100%;
        }
        .popup-content button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            margin: 10px;
            cursor: pointer;
        }
        .popup-content button.cancel {
            background-color: #e74c3c;
        }
    </style>
    <script>
        function showConfirmationPopup(id) {
            const popup = document.getElementById("confirmation-popup");
            const deleteBtn = document.getElementById("delete-btn");
            const cancelBtn = document.getElementById("cancel-btn");

            popup.style.display = "flex";
            
            deleteBtn.onclick = function() {
                window.location.href = '?delete=' + id;
            }

            cancelBtn.onclick = function() {
                popup.style.display = "none";
            }
        }
    </script>
</head>
<body>

<div class="form-container">
    <h2>નવી વસ્તુ સામગ્રી ઉમેરો </h2>
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" required>

        <label>માપ એકેમ પસંદ કરો:</label>
        <?php foreach ($all_units as $unit): ?>
            <input type="checkbox" name="units[]" value="<?= $unit ?>"> <?= $unit ?><br>
        <?php endforeach; ?>

        <button type="submit">નવી વસ્તુ સામગ્રી ઉમેરો </button>
    </form>
</div>

<hr>

<h3>વસ્તુ સામગ્રી ના નામ </h3>

<table>
    <tr>
        <th>ID</th>
        <th>નામ </th>
        <th>માપ એકેમ</th>
        <th>વસ્તુ સામગ્રી મેનેજ</th>
    </tr>
    <?php
    $result = $conn->query("SELECT * FROM ingredients ORDER BY id DESC");
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $unit_res = $conn->query("SELECT unit FROM ingredient_units WHERE ingredient_id=$id");
        $units = [];
        while ($u = $unit_res->fetch_assoc()) $units[] = $u['unit'];

        echo "<tr>
                <td>{$id}</td>
                <td>{$row['name']}</td>
                <td>" . implode(', ', $units) . "</td>
                <td>
                    <a href='edit_ingredient.php?id={$id}'>Edit</a> |
                    <span class='delete-btn' onclick='showConfirmationPopup({$id})'>Delete</span>
                </td>
            </tr>";
    }
    ?>
</table>

<div id="confirmation-popup" class="confirmation-popup">
    <div class="popup-content">
        <h4>Are you sure you want to delete this ingredient?</h4>
        <button id="delete-btn">Yes, Delete</button>
        <button id="cancel-btn" class="cancel">Cancel</button>
    </div>
</div>

</body>
</html>
