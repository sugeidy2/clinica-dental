<?php
require_once __DIR__ . '/includes/init.php';
if (!empty($_SESSION['uid'])) { header('Location: dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Iniciar sesión · <?= h(NOMBRE_CLINICA) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="login-wrap p-3">
  <div class="card shadow-lg border-0" style="max-width:400px;width:100%;border-radius:1rem">
    <div class="card-body p-4">
      <div class="text-center mb-4">
        <i class="bi bi-heart-pulse text-primary" style="font-size:3rem"></i>
        <h4 class="mt-2"><?= h(NOMBRE_CLINICA) ?></h4>
        <p class="text-muted mb-0">Historia clínica odontológica</p>
      </div>
      <div id="alerta" class="alert alert-danger d-none"></div>
      <form id="frm">
        <div class="mb-3"><label class="form-label">Usuario</label>
          <div class="input-group"><span class="input-group-text"><i class="bi bi-person"></i></span><input name="usuario" class="form-control" required autofocus></div></div>
        <div class="mb-4"><label class="form-label">Contraseña</label>
          <div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span><input type="password" name="password" class="form-control" required></div></div>
        <button class="btn btn-primary w-100" id="btn">Ingresar</button>
      </form>
    </div>
  </div>
</div>
<script>
document.getElementById('frm').addEventListener('submit', async e => {
  e.preventDefault();
  const btn = document.getElementById('btn'); btn.disabled = true;
  const r = await fetch('api/login.php', {method:'POST', body:new FormData(e.target)}).then(r => r.json());
  if (r.ok) location.href = 'dashboard.php';
  else { const a = document.getElementById('alerta'); a.textContent = r.msg; a.classList.remove('d-none'); btn.disabled = false; }
});
</script>
</body>
</html>
