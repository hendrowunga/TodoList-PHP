<?php
$servername = "localhost";
$username = "root"; // Ganti dengan username MySQL Anda
$password = ""; // Ganti dengan password MySQL Anda
$dbname = "belajarphp_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Fungsi untuk menyimpan tugas
function saveTodo($todo)
{
    global $conn;
    $sql = "INSERT INTO todos (todo, status) VALUES ('$todo', 'Belum Selesai')";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Fungsi untuk mengubah status tugas
function changeTodoStatus($id, $status)
{
    global $conn;
    $sql = "UPDATE todos SET status = '$status' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Fungsi untuk menghapus tugas berdasarkan nama
function deleteTodoByName($todo)
{
    global $conn;
    $sql = "DELETE FROM todos WHERE todo = '$todo'";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Halaman to-do list
session_start();
if (isset($_SESSION["username"])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["add_todo"])) {
            $todo = $_POST["todo"];
            if (saveTodo($todo)) {
                // Tidak menampilkan pesan saat berhasil menambahkan tugas
            } else {
                echo "Terjadi kesalahan saat menyimpan tugas.";
            }
        } elseif (isset($_POST["change_status"])) {
            $id = $_POST["id"];
            $status = $_POST["status"];
            if (changeTodoStatus($id, $status)) {
            } else {
                echo "Terjadi kesalahan saat mengubah status tugas.";
            }
        } elseif (isset($_POST["delete_todo"])) {
            $todo = $_POST["todo"];
            if (deleteTodoByName($todo)) {
                echo "<script>";
                echo "var todoElement = document.getElementById('$todo');";
                echo "if (todoElement) {";
                echo "    todoElement.remove();";
                echo "}";
                echo "</script>";
            } else {
                echo "Terjadi kesalahan saat menghapus tugas.";
            }
        }
    }

    // Tampilkan daftar tugas
    $sql = "SELECT * FROM todos";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>To Do List</title>
    <style>
    /* Style untuk halaman to-do list */
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
    }

    .todo-box {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 0 auto;
        margin-top: 50px;
    }

    .todo-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 5px;
        background-color: #f2f2f2;
    }

    .todo-item.completed {
        background-color: #e6ffe6;
    }

    .todo-item button {
        background-color: #4CAF50;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .todo-item button.red {
        background-color: #f44336;
    }

    input[type=text] {
        width: calc(100% - 140px);
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .action-button-container {
        display: flex;
        align-items: center;
    }

    .action-button-container button {
        margin-left: 10px;
        padding: 12px 20px;
    }
    </style>
</head>

<body>
    <div class="todo-box">
        <h2>To Do List</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="action-button-container">
                <input type="text" id="todo" name="todo" placeholder="Teks to do" required>
                <button type="submit" name="add_todo">Tambah</button>
            </div>
        </form>
        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $status = $row["status"];
                    $class = ($status == "Selesai") ? "completed" : "";
                    echo "<div class='todo-item $class' id='" . $row["todo"] . "'>";
                    echo $row["todo"];
                    echo "<div class='action-button-container'>";
                    if ($status == "Belum Selesai") {
                        echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
                        echo "<input type='hidden' name='id' value='" . $row["id"] . "'>";
                        echo "<input type='hidden' name='status' value='Selesai'>";
                        echo "<button type='submit' name='change_status'>Selesai</button>";
                    } else {
                        echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
                        echo "<input type='hidden' name='id' value='" . $row["id"] . "'>";
                        echo "<input type='hidden' name='status' value='Belum Selesai'>";
                        echo "<button type='submit' name='change_status'>Belum Selesai</button>";
                    }
                    echo "</form>";
                    echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
                    echo "<input type='hidden' name='todo' value='" . $row["todo"] . "'>";
                    echo "<button type='submit' name='delete_todo' class='red'>Hapus</button>";
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";
                }
            }
            ?>
    </div>
</body>

</html>
<?php
} else {
    echo "Anda harus login terlebih dahulu.";
}
?>