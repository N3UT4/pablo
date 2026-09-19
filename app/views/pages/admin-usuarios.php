<?php
$usuarios = $usuarios ?? [];
$rolLabels = ['cliente' => 'Cliente', 'tatuador' => 'Tatuador', 'admin' => 'Admin'];
$rolClasses = ['cliente' => 'badge-cliente', 'tatuador' => 'badge-tatuador', 'admin' => 'badge-admin'];
?>
<div class="container container-narrow" style="padding-top:24px;">
  <div style="margin-bottom:16px;">
    <a href="javascript:history.back()" class="btn-outline btn-sm" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Regresar</a>
  </div>

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <div class="glass-card" style="flex:1;min-width:280px;margin:0;">
            <div class="dash-section-title">
                <i class="fa-solid fa-users"></i>
                <h2>Usuarios y Tatuadores</h2>
            </div>
        </div>
        <button type="button" class="btn-glow" id="btnNewUser">
            <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
        </button>
    </div>

    <div class="glass-card" id="userFormCard" style="display:none;margin-bottom:20px;">
        <div class="dash-section-title">
            <i class="fa-solid fa-user-plus"></i>
            <h2 id="userFormTitle">Crear Usuario</h2>
        </div>
        <form method="POST" action="<?= BASE_URL ?>index.php?action=admin-save-user" class="dash-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <input type="hidden" name="user_id" id="userId" value="">
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre completo</label>
                    <input type="text" name="nombre" id="userNombre" required>
                </div>
                <div class="form-group">
                    <label>Correo electrónico</label>
                    <input type="email" name="email" id="userEmail" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" id="userTelefono">
                </div>
                <div class="form-group">
                    <label>Documento</label>
                    <input type="text" name="documento" id="userDocumento">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Rol</label>
                    <select name="rol" id="userRol">
                        <option value="cliente">Cliente</option>
                        <option value="tatuador">Tatuador</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Contraseña (solo si deseas cambiar)</label>
                    <input type="password" name="password" placeholder="Dejar vacío para no cambiar">
                </div>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn-glow">Guardar</button>
                <button type="button" class="btn-outline" id="btnCancelUser">Cancelar</button>
            </div>
        </form>
    </div>

    <div class="dash-table-wrapper">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Documento</th>
                    <th>Rol</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= (int) $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['telefono'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($u['documento'] ?? '-') ?></td>
                        <td><span class="badge <?= $rolClasses[$u['rol']] ?? 'badge-cliente' ?>"><?= $rolLabels[$u['rol']] ?? $u['rol'] ?></span></td>
                        <td style="font-size:12px;color:var(--bone-dim);"><?= htmlspecialchars($u['created_at'] ?? '-') ?></td>
                        <td>
                            <button type="button" class="btn-outline btn-sm" onclick="editUser(<?= (int) $u['id'] ?>, '<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>', '<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>', '<?= htmlspecialchars($u['telefono'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($u['documento'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($u['rol'] ?? 'cliente', ENT_QUOTES) ?>')">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:var(--bone-dim);">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function editUser(id, nombre, email, telefono, documento, rol) {
    document.getElementById('userFormCard').style.display = 'block';
    document.getElementById('userFormTitle').textContent = 'Editar Usuario';
    document.getElementById('userId').value = id;
    document.getElementById('userNombre').value = nombre;
    document.getElementById('userEmail').value = email;
    document.getElementById('userTelefono').value = telefono;
    document.getElementById('userDocumento').value = documento;
    document.getElementById('userRol').value = rol;
    document.getElementById('userFormCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
}
document.getElementById('btnNewUser').addEventListener('click', function() {
    document.getElementById('userFormCard').style.display = 'block';
    document.getElementById('userFormTitle').textContent = 'Crear Usuario';
    document.getElementById('userId').value = '';
    document.getElementById('userNombre').value = '';
    document.getElementById('userEmail').value = '';
    document.getElementById('userTelefono').value = '';
    document.getElementById('userDocumento').value = '';
    document.getElementById('userRol').value = 'cliente';
});
document.getElementById('btnCancelUser').addEventListener('click', function() {
    document.getElementById('userFormCard').style.display = 'none';
});
</script>
