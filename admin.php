<?php 
session_start();
require "config.php";

$login_error = "";

// ---------------- LOGIN ----------------
if (isset($_GET["logout"])) {
    unset($_SESSION["admin"]);
    header("Location: admin.php");
    exit;
}

if (!isset($_SESSION["admin"])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $u = $_POST["username"];
        $p = $_POST["password"];

        if ($u === "admin" && $p === "admin123") {
            $_SESSION["admin"] = true;
        } else {
            $login_error = "Invalid username or password";
        }
    }

    if (!isset($_SESSION["admin"])) {
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<style>
body { font-family: Arial; background:#f3f4f6; display:flex; justify-content:center; align-items:center; height:100vh;}
.card { background:#fff; padding:20px; width:300px; border-radius:10px; box-shadow:0 6px 20px rgba(0,0,0,0.2); }
input { width:100%; padding:10px; margin-top:10px; border-radius:6px; border:1px solid #ccc; }
button { width:100%; margin-top:15px; padding:10px; background:#6a11cb; color:#fff; border:none; border-radius:6px; }
</style>
</head>
<body>
<div class="card">
<h2>Admin Login</h2>
<form method="POST">
<input type="text" name="username" placeholder="admin">
<input type="password" name="password" placeholder="admin123">
<button>Login</button>
<p style="color:red;"><?= $login_error ?></p>
</form>
</div>
</body>
</html>
<?php
exit;
    }
}
// ---------------- END LOGIN ----------------


// ---------------- FILTERS ----------------
$single_date = $_GET["single_date"] ?? "";   // new filter

?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Panel</title>
<style>
body { font-family: Arial; background:#f3f4f6; margin:0; }
.top { background:#6a11cb; color:#fff; padding:12px 20px; display:flex; justify-content:space-between; align-items:center; }
.box { background:#fff; margin:20px auto; padding:20px; width:90%; border-radius:10px; box-shadow:0 6px 20px rgba(0,0,0,0.1); }
table { width:100%; border-collapse:collapse; margin-top:10px; }
th, td { border-bottom:1px solid #ddd; padding:10px; text-align:left; }
th { background:#eee; }
.logout { color:#fff; text-decoration:none; }
.filter-box { margin:10px 0; }
.filter-box input[type=date] {
    padding:8px; border-radius:6px; border:1px solid #bbb;
}
.filter-box button {
    padding:8px 12px; background:#6a11cb; color:white; border:none;
    border-radius:6px; cursor:pointer;
}
</style>
</head>
<body>

<div class="top">
    <h2>UPI Checker Admin Panel</h2>
    <a class="logout" href="admin.php?logout=1">Logout</a>
</div>

<!-- -------------- SINGLE DATE FILTER ---------------- -->
<div class="box">
    <h3>Filter Data by Single Date</h3>

    <form method="GET" class="filter-box">
        <input type="date" name="single_date" value="<?= $single_date ?>">
        <button type="submit">Apply</button>
    </form>

    <p style="color:#6a11cb; font-weight:bold;">
        <?= $single_date ? "Showing results for: $single_date" : "Showing all results" ?>
    </p>
</div>


<!-- -------------- VALID CHECKS ---------------- -->
<div class="box">
    <h3>Valid UPI Checks</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>UPI ID</th>
            <th>Checked At</th>
            <th>IP</th>
        </tr>
        <?php
        if ($single_date) {
            $stmt = $pdo->prepare("SELECT * FROM upi_checks WHERE DATE(checked_at)=?");
            $stmt->execute([$single_date]);
            $rows = $stmt->fetchAll();
        } else {
            $rows = $pdo->query("SELECT * FROM upi_checks ORDER BY checked_at DESC")->fetchAll();
        }

        foreach ($rows as $r) {
            echo "<tr>
                    <td>{$r['id']}</td>
                    <td>{$r['upi_id']}</td>
                    <td>{$r['checked_at']}</td>
                    <td>{$r['ip_address']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>

<!-- -------------- REPORTS ---------------- -->
<div class="box">
    <h3>Fake / Invalid UPI Reports</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>UPI ID</th>
            <th>Reason</th>
            <th>Reported At</th>
            <th>IP</th>
        </tr>
        <?php
        if ($single_date) {
            $stmt = $pdo->prepare("SELECT * FROM upi_reports WHERE DATE(reported_at)=?");
            $stmt->execute([$single_date]);
            $rows = $stmt->fetchAll();
        } else {
            $rows = $pdo->query("SELECT * FROM upi_reports ORDER BY reported_at DESC")->fetchAll();
        }

        foreach ($rows as $r) {
            echo "<tr>
                    <td>{$r['id']}</td>
                    <td>{$r['upi_id']}</td>
                    <td>{$r['reason']}</td>
                    <td>{$r['reported_at']}</td>
                    <td>{$r['ip_address']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
