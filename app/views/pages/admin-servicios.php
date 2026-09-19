<?php
$servicios = $servicios ?? [];
?>
<div class="container container-narrow" style="padding-top:24px;">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
  </div>

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <div class="glass-card" style="flex:1;min-width:280px;margin:0;">
            <div class="dash-section-title">
                <i class="fa-solid fa-scissors"></i>
                <h2>Servicios y Precios</h2>
            </div>
        </div>
        <button type="button" class="btn-glow" id="btnNewService">
            <i class="fa-solid fa-plus"></i> Nuevo Servicio
        </button>
    </div>

    <div class="glass-card" id="serviceFormCard" style="display:none;margin-bottom:20px;">
        <div class="dash-section-title">
            <i class="fa-solid fa-scissors"></i>
            <h2 id="serviceFormTitle">Crear Servicio</h2>
        </div>
        <form method="POST" action="<?= BASE_URL ?>index.php?action=admin-save-service" class="dash-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <input type="hidden" name="servicio_id" id="serviceId" value="">
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre del servicio</label>
                    <input type="text" name="nombre" id="serviceNombre" required>
                </div>
                <div class="form-group">
                    <label>Slug (identificador)</label>
                    <input type="text" name="slug" id="serviceSlug" required placeholder="ej: blackwork">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Precio desde</label>
                    <input type="number" name="precio_desde" id="servicePrecio" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <input type="text" name="descripcion" id="serviceDescripcion">
                </div>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn-glow">Guardar</button>
                <button type="button" class="btn-outline" id="btnCancelService">Cancelar</button>
            </div>
        </form>
    </div>

    <div class="dash-table-wrapper">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Servicio</th>
                    <th>Slug</th>
                    <th>Precio Desde</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicios as $s): ?>
                    <tr>
                        <td><?= (int) $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['nombre']) ?></td>
                        <td><code style="color:var(--blood-bright);font-size:12px;"><?= htmlspecialchars($s['slug']) ?></code></td>
                        <td>$<?= htmlspecialchars($s['precio_desde'] ?? '0') ?></td>
                        <td style="font-size:12px;color:var(--bone-dim);max-width:200px;"><?= htmlspecialchars(substr($s['descripcion'] ?? '', 0, 60)) ?></td>
                        <td><span class="badge badge-completada">Activo</span></td>
                        <td>
                            <button type="button" class="btn-outline btn-sm" onclick="editService(<?= (int) $s['id'] ?>, '<?= htmlspecialchars($s['nombre'], ENT_QUOTES) ?>', '<?= htmlspecialchars($s['slug'], ENT_QUOTES) ?>', '<?= htmlspecialchars($s['precio_desde'] ?? '0', ENT_QUOTES) ?>', '<?= htmlspecialchars($s['descripcion'] ?? '', ENT_QUOTES) ?>')">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" class="btn-outline btn-sm" style="color:#ef4444;border-color:rgba(239,68,68,0.3);" data-delete-id="<?= (int) $s['id'] ?>">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($servicios)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:var(--bone-dim);">
                            No hay servicios registrados.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function editService(id, nombre, slug, precio, descripcion) {
    document.getElementById('serviceFormCard').style.display = 'block';
    document.getElementById('serviceFormTitle').textContent = 'Editar Servicio';
    document.getElementById('serviceId').value = id;
    document.getElementById('serviceNombre').value = nombre;
    document.getElementById('serviceSlug').value = slug;
    document.getElementById('servicePrecio').value = precio;
    document.getElementById('serviceDescripcion').value = descripcion;
    document.getElementById('serviceFormCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
}
document.getElementById('btnNewService').addEventListener('click', function() {
    document.getElementById('serviceFormCard').style.display = 'block';
    document.getElementById('serviceFormTitle').textContent = 'Crear Servicio';
    document.getElementById('serviceId').value = '';
    document.getElementById('serviceNombre').value = '';
    document.getElementById('serviceSlug').value = '';
    document.getElementById('servicePrecio').value = '';
    document.getElementById('serviceDescripcion').value = '';
});
document.getElementById('btnCancelService').addEventListener('click', function() {
    document.getElementById('serviceFormCard').style.display = 'none';
});
document.querySelectorAll('[data-delete-id]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        if (confirm('¿Eliminar este servicio?')) {
            window.location.href = '<?= BASE_URL ?>index.php?action=admin-delete-service&id=' + this.dataset.deleteId;
        }
    });
});
</script>
