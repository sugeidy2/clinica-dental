<?php $titulo = 'Procedimientos'; $active = 'procedimientos'; require __DIR__ . '/includes/header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h3 class="m-0">Procedimientos odontológicos</h3>
  <button class="btn btn-primary" onclick="nuevo()"><i class="bi bi-plus-lg me-1"></i>Nuevo procedimiento</button>
</div>
<div class="card stat-card"><div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle">
  <thead><tr><th>Procedimiento</th><th>Descripción</th><th>Precio</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
  <tbody id="lista"></tbody></table></div></div></div>

<div class="modal fade" id="mdl" tabindex="-1"><div class="modal-dialog"><form class="modal-content" id="frm">
  <div class="modal-header"><h5 class="modal-title" id="mdlTitulo">Procedimiento</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body"><input type="hidden" name="id" id="id">
    <div class="mb-3"><label class="form-label">Nombre *</label><input name="nombre" id="nombre" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Descripción</label><input name="descripcion" id="descripcion" class="form-control"></div>
    <div class="row"><div class="col-6 mb-3"><label class="form-label">Precio *</label><input type="number" min="0" step="0.01" name="precio" id="precio" class="form-control" required></div>
    <div class="col-6 mb-3"><label class="form-label">Estado</label><select name="activo" id="activo" class="form-select"><option value="1">Activo</option><option value="0">Inactivo</option></select></div></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar</button></div>
</form></div></div>

<script>
let modal, datos = [];
async function cargar() {
  const r = await api('api/procedimientos.php?action=list'); datos = r.data;
  lista.innerHTML = datos.map(p => `<tr><td><b>${esc(p.nombre)}</b></td><td>${esc(p.descripcion)}</td><td>${money(p.precio)}</td>
    <td><span class="badge text-bg-${p.activo == 1 ? 'success' : 'secondary'}">${p.activo == 1 ? 'Activo' : 'Inactivo'}</span></td>
    <td class="text-end text-nowrap"><button class="btn btn-sm btn-outline-primary" onclick="editar(${p.id})"><i class="bi bi-pencil"></i></button>
    <button class="btn btn-sm btn-outline-secondary" onclick="eliminar(${p.id})"><i class="bi bi-trash"></i></button></td></tr>`).join('')
    || '<tr><td colspan="5" class="text-center text-muted">Sin procedimientos.</td></tr>';
}
function nuevo() { frm.reset(); id.value = ''; mdlTitulo.textContent = 'Nuevo procedimiento'; modal.show(); }
function editar(i) {
  const p = datos.find(x => x.id == i);
  ['id','nombre','descripcion','precio','activo'].forEach(c => document.getElementById(c).value = p[c] ?? '');
  mdlTitulo.textContent = 'Editar procedimiento'; modal.show();
}
async function eliminar(i) {
  if (!confirm('¿Eliminar este procedimiento?')) return;
  const r = await api('api/procedimientos.php?action=delete', {id: i}); toast(r.msg, r.ok); cargar();
}
document.addEventListener('DOMContentLoaded', () => {
  modal = new bootstrap.Modal(mdl);
  frm.addEventListener('submit', async e => {
    e.preventDefault();
    const r = await api('api/procedimientos.php?action=save', new FormData(frm));
    toast(r.msg, r.ok); if (r.ok) { modal.hide(); cargar(); }
  });
  cargar();
});
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
