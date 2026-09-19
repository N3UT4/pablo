<?php
// Partial: Mensajes flash (éxito/error).
// Requiere: $flash
?>
<?php if ($flash): ?>
    <div style="padding:0 24px;margin-bottom:16px;">
        <div class="alert show <?= $flash['type'] === 'success' ? 'success' : '' ?>">
            <span><?= htmlspecialchars($flash['message']) ?></span>
        </div>
    </div>
<?php endif; ?>
