<?php
include 'db.php';
include 'navbar.php';
session_start();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_no = strtoupper(trim($_POST['bus_no']));
    $pattern = "/^[A-Z]{2}-\d{2}-[A-Z]{1}-\d{4}$/";

    // Server-side validation
    if (!preg_match($pattern, $bus_no)) {
        echo "<script>alert('Invalid Bus Number Format. Use GJ-18-A-0001');</script>";
    } else {
        $check = $conn->query("SELECT * FROM buses WHERE bus_no = '$bus_no'");
        if ($check->num_rows > 0) {
            echo "<script>alert('એ બસ પેહેલે થી જ ઉમેરાઈ ગઈ છે ');</script>";
        } else {
            $insert = $conn->query("INSERT INTO buses (bus_no) VALUES ('$bus_no')");
            if ($insert) {
                echo "<script>alert('બસ ઉમેરો ગઈ છે '); window.location='add_bus.php';</script>";
            } else {
                echo "<script>alert('બસ ઉમેરો ગઈ નથી ફરીવાર નાખો ');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>બસ ઉમેરો  - સારથી ટ્રાવેલ્સ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

    <div class="max-w-4xl mx-auto py-10 px-6">
        <h2 class="text-3xl font-bold mb-6 text-center text-indigo-600 animate-pulse">🚌 બસ ઉમેરો </h2>

        <form method="POST" onsubmit="return validateBusNo()" class="bg-white shadow-md rounded-lg p-6 mb-10">
            <label class="block text-lg font-semibold mb-2">બસ નંબર  
                <span class="text-sm text-gray-500">બસ નંબર એ રીતે જ નાખવો (GJ-18-A-0001)</span>
            </label>
            <input 
                type="text" 
                name="bus_no" 
                id="bus_no" 
                oninput="this.value = this.value.toUpperCase()"
                required 
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                placeholder="અહીંયા બસ નંબર નાખો"
            >
            <button 
                type="submit" 
                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300"
            >
                ➕ બસ ઉમેરો
            </button>
        </form>

        <h3 class="text-2xl font-semibold mb-4">🗂️ દાખલ કરેલી બધી બસ </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">ID</th>
                        <th class="py-3 px-4 text-left">બસ નંબર</th>
                        <th class="py-3 px-4 text-left">બસ મેનેજ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM buses ORDER BY id DESC");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='border-b hover:bg-gray-100 transition'>
                                <td class='py-2 px-4'>{$row['id']}</td>
                                <td class='py-2 px-4 font-mono'>{$row['bus_no']}</td>
                                <td class='py-2 px-4'>
                                    <a href='edit_bus.php?id={$row['id']}' class='text-blue-600 hover:underline'>Edit</a> | 
                                    <a href='delete_bus.php?id={$row['id']}' class='text-red-600 hover:underline' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function validateBusNo() {
            const busNo = document.getElementById("bus_no").value.trim().toUpperCase();
            const pattern = /^[A-Z]{2}-\d{2}-[A-Z]{1}-\d{4}$/;

            if (!pattern.test(busNo)) {
                alert("❌ બસ નંબર ફરીવાર નાખો જે રીતે કીધું છે. એ રીતે નાખો  GJ-18-A-0001");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
