<?php $titulo = 'Panel principal'; $active = 'panel'; require __DIR__ . '/includes/header.php'; ?>
<h3 class="mb-4">Panel principal</h3>
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3"><div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
    <div><div class="text-muted small">Pacientes</div><div class="fs-3 fw-bold" id="s_pac">–</div></div><i class="bi bi-people stat-icon text-primary"></i></div></div></div>
  <div class="col-6 col-xl-3"><div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
    <div><div class="text-muted small">Atenciones totales</div><div class="fs-3 fw-bold" id="s_att">–</div></div><i class="bi bi-journal-medical stat-icon text-success"></i></div></div></div>
  <div class="col-6 col-xl-3"><div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
    <div><div class="text-muted small">Atenciones del mes</div><div class="fs-3 fw-bold" id="s_mes">–</div></div><i class="bi bi-calendar-check stat-icon text-warning"></i></div></div></div>
  <div class="col-6 col-xl-3"><div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
    <div><div class="text-muted small">Ingresos del mes</div><div class="fs-3 fw-bold" id="s_ing">–</div></div><i class="bi bi-cash-coin stat-icon text-danger"></i></div></div></div>
</div>
<div class="row g-3">
  <div class="col-lg-5"><div class="card stat-card h-100"><div class="card-body">
    <h6 class="mb-3">Procedimientos más realizados</h6><div id="porProc"></div></div></div></div>
  <div class="col-lg-7"><div class="card stat-card h-100"><div class="card-body">
    <h6 class="mb-3">Últimas atenciones</h6>
    <div class="table-responsive"><table class="table table-sm align-middle">
      <thead><tr><th>Fecha</th><th>Paciente</th><th>Procedimiento</th><th></th></tr></thead><tbody id="recientes"></tbody></table></div></div></div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', async () => {
  const d = await api('api/stats.php');
  s_pac.textContent = d.pacientes; s_att.textContent = d.atenciones; s_mes.textContent = d.mes; s_ing.textContent = money(d.ingresos);
  const max = Math.max(1, ...d.por_proc.map(x => +x.total));
  porProc.innerHTML = d.por_proc.length ? d.por_proc.map(x => `
    <div class="mb-3"><div class="d-flex justify-content-between small"><span>${esc(x.nombre)}</span><b>${x.total}</b></div>
    <div class="progress" style="height:8px"><div class="progress-bar" style="width:${x.total / max * 100}%"></div></div></div>`).join('')
    : '<p class="text-muted">Aún no hay atenciones registradas.</p>';
  recientes.innerHTML = d.recientes.length ? d.recientes.map(x => `<tr><td>${esc(x.fecha)}</td><td>${esc(x.paciente)}</td><td>${esc(x.procedimiento)}</td>
    <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="historia.php?id=${x.paciente_id}">Ver</a></td></tr>`).join('')
    : '<tr><td colspan="4" class="text-muted">Sin registros.</td></tr>';
});
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
