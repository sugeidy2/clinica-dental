<?php
$titulo = 'Historia clínica'; $active = 'pacientes';
require __DIR__ . '/includes/header.php';
$st = db()->prepare('SELECT * FROM pacientes WHERE id = ?');
$st->execute([(int)($_GET['id'] ?? 0)]);
$p = $st->fetch();
if (!$p) { echo '<div class="alert alert-warning">Paciente no encontrado. <a href="pacientes.php">Volver</a></div>'; require __DIR__ . '/includes/footer.php'; exit; }
$procs = db()->query('SELECT id,nombre,precio FROM procedimientos WHERE activo=1 ORDER BY nombre')->fetchAll();
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h3 class="m-0">Historia clínica</h3>
  <div class="d-flex gap-2">
    <a href="pacientes.php" class="btn btn-light"><i class="bi bi-arrow-left"></i> Volver</a>
    <a href="pdf/historia_pdf.php?id=<?= (int)$p['id'] ?>" target="_blank" class="btn btn-danger"><i class="bi bi-file-earmark-pdf me-1"></i>Generar PDF</a>
    <button class="btn btn-primary" onclick="nueva()"><i class="bi bi-plus-lg me-1"></i>Nueva atención</button>
  </div>
</div>

<div class="card stat-card mb-4"><div class="card-body">
  <div class="row g-3">
    <div class="col-md-6"><h4 class="mb-1"><?= h($p['nombres'] . ' ' . $p['apellidos']) ?></h4>
      <div class="text-muted">Documento: <?= h($p['documento']) ?></div>
      <div class="text-muted">Nacimiento: <?= h($p['fecha_nacimiento'] ?: '—') ?> <span id="edad"></span> · Sexo: <?= h($p['sexo']) ?></div>
      <div class="text-muted">Tel: <?= h($p['telefono'] ?: '—') ?> · <?= h($p['email'] ?: '—') ?></div>
      <div class="text-muted"><?= h($p['direccion'] ?: '') ?></div></div>
    <div class="col-md-6">
      <div class="small"><b class="text-danger">Alergias:</b> <?= nl2br(h($p['alergias'] ?: 'Ninguna registrada')) ?></div>
      <div class="small"><b>Antecedentes:</b> <?= nl2br(h($p['enfermedades'] ?: 'Ninguno registrado')) ?></div>
      <div class="small"><b>Medicamentos:</b> <?= nl2br(h($p['medicamentos'] ?: 'Ninguno registrado')) ?></div></div>
  </div>
</div></div>

<div class="card stat-card"><div class="card-body">
  <div class="d-flex justify-content-between mb-3"><h5 class="m-0">Procedimientos realizados</h5><div class="fw-semibold">Total: <span id="total">$0</span></div></div>
  <div id="atenciones"></div>
</div></div>

<div class="modal fade" id="mdl" tabindex="-1"><div class="modal-dialog modal-lg"><form class="modal-content" id="frm">
  <div class="modal-header"><h5 class="modal-title" id="mdlTitulo">Atención</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body"><input type="hidden" name="id" id="id"><input type="hidden" name="paciente_id" value="<?= (int)$p['id'] ?>">
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">Procedimiento *</label>
        <select name="procedimiento_id" id="procedimiento_id" class="form-select" required>
          <option value="">Seleccione...</option>
          <?php foreach ($procs as $pr): ?><option value="<?= (int)$pr['id'] ?>" data-precio="<?= h($pr['precio']) ?>"><?= h($pr['nombre']) ?></option><?php endforeach; ?>
        </select></div>
      <div class="col-md-3"><label class="form-label">Fecha *</label><input type="date" name="fecha" id="fecha" class="form-control" required></div>
      <div class="col-md-3"><label class="form-label">Pieza dental</label><input name="pieza_dental" id="pieza_dental" class="form-control" placeholder="Ej: 36, 48"></div>
      <div class="col-12"><label class="form-label">Diagnóstico</label><textarea name="diagnostico" id="diagnostico" rows="2" class="form-control"></textarea></div>
      <div class="col-12"><label class="form-label">Observaciones / tratamiento realizado</label><textarea name="observaciones" id="observaciones" rows="3" class="form-control"></textarea></div>
      <div class="col-md-6"><label class="form-label">Costo</label><input type="number" min="0" step="0.01" name="costo" id="costo" class="form-control" value="0"></div>
      <div class="col-md-6"><label class="form-label">Estado</label><select name="estado" id="estado" class="form-select"><option>Realizado</option><option>Pendiente</option></select></div>
    </div></div>
  <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar</button></div>
</form></div></div>

<script>
const PID = <?= (int)$p['id'] ?>;
let modal, datos = [];
async function cargar() {
  const r = await api('api/historia.php?action=list&paciente_id=' + PID); datos = r.data;
  total.textContent = money(datos.filter(a => a.estado === 'Realizado').reduce((s, a) => s + +a.costo, 0));
  atenciones.innerHTML = datos.length ? datos.map(a => `<div class="timeline-item">
    <div class="d-flex justify-content-between flex-wrap gap-2">
      <div><b>${esc(a.procedimiento)}</b> ${a.pieza_dental ? `<span class="badge text-bg-light border">Pieza ${esc(a.pieza_dental)}</span>` : ''}
        <span class="badge text-bg-${a.estado === 'Realizado' ? 'success' : 'warning'}">${a.estado}</span>
        <div class="small text-muted">${esc(a.fecha)} · ${money(a.costo)}</div></div>
      <div class="text-nowrap"><button class="btn btn-sm btn-outline-primary" onclick="editar(${a.id})"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-outline-secondary" onclick="eliminar(${a.id})"><i class="bi bi-trash"></i></button></div></div>
    ${a.diagnostico ? `<div class="small mt-1"><b>Diagnóstico:</b> ${esc(a.diagnostico)}</div>` : ''}
    ${a.observaciones ? `<div class="small"><b>Observaciones:</b> ${esc(a.observaciones)}</div>` : ''}</div>`).join('')
    : '<p class="text-muted">Este paciente aún no tiene procedimientos registrados.</p>';
}
function nueva() { frm.reset(); id.value = ''; fecha.value = new Date().toISOString().slice(0, 10); mdlTitulo.textContent = 'Nueva atención'; modal.show(); }
function editar(i) {
  const a = datos.find(x => x.id == i);
  ['id','procedimiento_id','fecha','pieza_dental','diagnostico','observaciones','costo','estado'].forEach(c => document.getElementById(c).value = a[c] ?? '');
  mdlTitulo.textContent = 'Editar atención'; modal.show();
}
async function eliminar(i) {
  if (!confirm('¿Eliminar esta atención?')) return;
  const r = await api('api/historia.php?action=delete', {id: i}); toast(r.msg, r.ok); cargar();
}
document.addEventListener('DOMContentLoaded', () => {
  modal = new bootstrap.Modal(mdl);
  document.getElementById('edad').textContent = edad(<?= json_encode($p['fecha_nacimiento']) ?>) ? '(' + edad(<?= json_encode($p['fecha_nacimiento']) ?>) + ')' : '';
  procedimiento_id.addEventListener('change', () => {
    const o = procedimiento_id.selectedOptions[0]; if (o && o.dataset.precio) costo.value = o.dataset.precio;
  });
  frm.addEventListener('submit', async e => {
    e.preventDefault();
    const r = await api('api/historia.php?action=save', new FormData(frm));
    toast(r.msg, r.ok); if (r.ok) { modal.hide(); cargar(); }
  });
  cargar();
});
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
