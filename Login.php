<?php
session_start();

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header><h1>SCHOOL PORTAL</h1></header>

    <main class="container">

        <h2>Login</h2>

        <!-- Error Message -->
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color:red">
                <?= $_SESSION['error']; unset($_SESSION['error']);?> 
            </p>
        <?php endif; ?>

        <!-- Success Message -->
         <?php if(isset($_SESSION['success'])): ?>
            <p style="color:green">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </p>
        <?php endif; ?>

        <form action="actions/Process_Login.php" method="post">

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($old['email'] ?? '')?>" required>

            <label for="pwd">Password</label>
            <input type="password" name="pwd" id="pwd" required>

            <input type="submit" value="Login">

        </form>

        <p>Don't have an account? <a href="Register.php">Register here.</a></p>

    </main>

    <footer><p>&copy;2026 school portal by Mubarak</p></footer>

</body>
</html>