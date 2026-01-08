<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Ingreso</h2>

<?php if (!empty($error)): ?>
    <p style="color:red"><?= $error ?></p>
<?php endif; ?>

<form method="POST" action="/login">
    <label>RUT</label><br>
    <input type="text" name="rut" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Entrar</button>
</form>

</body>
</html>
