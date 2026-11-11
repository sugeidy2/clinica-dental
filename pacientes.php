<?php $titulo = 'Pacientes'; $active = 'pacientes'; require __DIR__ . '/includes/header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h3 class="m-0">Pacientes</h3>
  <button class="btn btn-primary" onclick="nuevo()"><i class="bi bi-person-plus me-1"></i>Nuevo paciente</button>
</div>
<div class="card stat-card"><div class="card-body">
  <input id="q" class="form-control mb-3" placeholder="Buscar por nombre, apellido o documento...">
  <div class="table-responsive"><table class="table table-hover align-middle">
    <thead><tr><th>Documento</th><th>Paciente</th><th>Edad</th><th>Teléfono</th><th class="text-end">Acciones</th></tr></thead>
    <tbody id="lista"></tbody></table></div>
</div></div>

<div class="modal fade" id="mdl" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-scrollable"><form class="modal-content" id="frm">
  <div class="modal-header"><h5 class="modal-title" id="mdlTitulo">Paciente</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body"><input type="hidden" name="id" id="id"><div class="row g-3">
    <div class="col-md-4"><label class="form-label">Documento *</label><input name="documento" id="documento" class="form-control" required></div>
    <div class="col-md-4"><label class="form-label">Nombres *</label><input name="nombres" id="nombres" class="form-control" required></div>
    <div class="col-md-4"><label class="form-label">Apellidos *</label><input name="apellidos" id="apellidos" class="form-control" required></div>
    <div class="col-md-4"><label class="form-label">Fecha de nacimiento</label><input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Sexo</label><select name="sexo" id="sexo" class="form-select"><option value="F">Femenino</option><option value="M">Masculino</option><option value="Otro">Otro</option></select></div>
    <div class="col-md-4"><label class="form-label">Teléfono</label><input name="telefono" id="telefono" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">Correo</label><input type="email" name="email" id="email" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">Dirección</label><input name="direccion" id="direccion" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Alergias</label><textarea name="alergias" id="alergias" rows="2" class="form-control"></textarea></div>
    <div class="col-md-4"><label class="form-label">Enfermedades / antecedentes</label><textarea name="enfermedades" id="enfermedades" rows="2" class="form-control"></textarea></div>
    <div class="col-md-4"><label class="form-label">Medicamentos actuales</label><textarea name="medicamentos" id="medicamentos" rows="2" class="form-control"></textarea></div>
  </div></div>
  <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar</button></div>
</form></div></div>

<script>
const campos = ['id','documento','nombres','apellidos','fecha_nacimiento','sexo','telefono','email','direccion','alergias','enfermedades','medicamentos'];
let modal, timer;
async function cargar() {
  const r = await api('api/pacientes.php?action=list&q=' + encodeURIComponent(q.value));
  lista.innerHTML = r.data.length ? r.data.map(p => `<tr>
    <td>${esc(p.documento)}</td><td><b>${esc(p.nombres)} ${esc(p.apellidos)}</b></td><td>${edad(p.fecha_nacimiento)}</td><td>${esc(p.telefono)}</td>
    <td class="text-end text-nowrap">
      <a class="btn btn-sm btn-success" href="historia.php?id=${p.id}" title="Historia clínica"><i class="bi bi-journal-medical"></i></a>
      <a class="btn btn-sm btn-outline-danger" href="pdf/historia_pdf.php?id=${p.id}" target="_blank" title="PDF"><i class="bi bi-file-earmark-pdf"></i></a>
      <button class="btn btn-sm btn-outline-primary" onclick="editar(${p.id})" title="Editar"><i class="bi bi-pencil"></i></button>
      <button class="btn btn-sm btn-outline-secondary" onclick="eliminar(${p.id})" title="Eliminar"><i class="bi bi-trash"></i></button>
    </td></tr>`).join('') : '<tr><td colspan="5" class="text-center text-muted">No hay pacientes registrados.</td></tr>';
}
function nuevo() { frm.reset(); id.value = ''; mdlTitulo.textContent = 'Nuevo paciente'; modal.show(); }
async function editar(i) {
  const r = await api('api/pacientes.php?action=get&id=' + i);
  campos.forEach(c => document.getElementById(c).value = r.data[c] ?? '');
  mdlTitulo.textContent = 'Editar paciente'; modal.show();
}
async function eliminar(i) {
  if (!confirm('¿Eliminar paciente y TODA su historia clínica?')) return;
  const r = await api('api/pacientes.php?action=delete', {id: i});
  toast(r.msg, r.ok); cargar();
}
document.addEventListener('DOMContentLoaded', () => {
  modal = new bootstrap.Modal(mdl);
  q.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(cargar, 250); });
  frm.addEventListener('submit', async e => {
    e.preventDefault();
    const r = await api('api/pacientes.php?action=save', new FormData(frm));
    toast(r.msg, r.ok); if (r.ok) { modal.hide(); cargar(); }
  });
  cargar();
});
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
