<style>
.sponsors-admin-wrap {
    max-width: 1100px;
    margin: 40px auto;
    padding: 0 20px 60px;
}
.sponsors-admin-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 28px;
    flex-wrap: wrap;
}
.sponsors-admin-header h2 {
    font-size: 26px;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0;
}
.btn-nuevo-sponsor {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #FF6600;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 22px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: background .2s;
}
.btn-nuevo-sponsor:hover { background: #e55a00; color: #fff; }

.sponsor-alert {
    padding: 12px 18px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 500;
}
.sponsor-alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.sponsor-alert.error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

.sponsors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}
.sponsor-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    transition: box-shadow .2s;
    border: 2px solid transparent;
}
.sponsor-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.13); }
.sponsor-card.inactivo { opacity: .6; border-color: #ddd; }

.sponsor-card-top {
    display: flex;
    align-items: center;
    gap: 14px;
}
.sponsor-logo-thumb {
    width: 64px;
    height: 64px;
    border-radius: 10px;
    object-fit: contain;
    background: #f5f5f5;
    border: 1px solid #eee;
    flex-shrink: 0;
}
.sponsor-logo-placeholder {
    width: 64px;
    height: 64px;
    border-radius: 10px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #bbb;
    font-size: 28px;
    flex-shrink: 0;
}
.sponsor-card-info { flex: 1; min-width: 0; }
.sponsor-card-info h3 {
    font-size: 16px;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0 0 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sponsor-card-info a.sitio {
    font-size: 12px;
    color: #FF6600;
    text-decoration: none;
    word-break: break-all;
}
.sponsor-card-info a.sitio:hover { text-decoration: underline; }

.sponsor-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.badge-sp {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .3px;
}
.badge-sp.activo   { background: #d4edda; color: #155724; }
.badge-sp.inactivo { background: #f8d7da; color: #721c24; }
.badge-sp.global   { background: #cce5ff; color: #004085; }
.badge-sp.torneo   { background: #fff3cd; color: #856404; }
.badge-sp.orden    { background: #f0f0f0; color: #555; }

.sponsor-social-icons {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 6px;
}
.ssi {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #fff;
    text-decoration: none;
    transition: opacity .15s;
}
.ssi:hover { opacity: .8; }
.ssi-web  { background: #555; }
.ssi-ig   { background: linear-gradient(135deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
.ssi-fb   { background: #1877f2; }
.ssi-wa   { background: #25d366; }
.ssi-otro { background: #888; }

.sponsor-card-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.btn-sp {
    flex: 1;
    min-width: 70px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 7px 10px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: opacity .15s;
    white-space: nowrap;
}
.btn-sp:hover { opacity: .82; }
.btn-sp.editar   { background: #e3f0ff; color: #0056b3; }
.btn-sp.toggle   { background: #fff3cd; color: #856404; }
.btn-sp.eliminar { background: #fde8e8; color: #c0392b; }

.sponsors-empty {
    text-align: center;
    padding: 60px 20px;
    color: #888;
}
.sponsors-empty i { font-size: 48px; margin-bottom: 16px; opacity: .3; }
.sponsors-empty p { font-size: 16px; margin: 0; }

@media (max-width: 600px) {
    .sponsors-admin-header { flex-direction: column; align-items: flex-start; }
    .sponsors-grid { grid-template-columns: 1fr; }
}
</style>

<div class="sponsors-admin-wrap">

    <div class="sponsors-admin-header">
        <h2><i class="fas fa-handshake"></i> Sponsors / Auspiciantes</h2>
        <a href="<?= base_url('admin/Sponsors/crear') ?>" class="btn-nuevo-sponsor">
            <i class="fas fa-plus"></i> Nuevo Sponsor
        </a>
    </div>

    <?php if (!empty($flash_success)): ?>
        <div class="sponsor-alert success"><?= htmlspecialchars($flash_success) ?></div>
    <?php endif; ?>
    <?php if (!empty($flash_error)): ?>
        <div class="sponsor-alert error"><?= htmlspecialchars($flash_error) ?></div>
    <?php endif; ?>

    <?php if (empty($sponsors)): ?>
        <div class="sponsors-empty">
            <i class="fas fa-handshake"></i>
            <p>No hay sponsors cargados todavía.<br>¡Agregá el primero!</p>
        </div>
    <?php else: ?>
        <div class="sponsors-grid">
            <?php foreach ($sponsors as $s): ?>
            <div class="sponsor-card <?= $s->activo ? '' : 'inactivo' ?>">

                <div class="sponsor-card-top">
                    <?php if ($s->logo): ?>
                        <img class="sponsor-logo-thumb"
                             src="data:image/png;base64,<?= $s->logo ?>"
                             alt="<?= htmlspecialchars($s->nombre) ?>">
                    <?php else: ?>
                        <div class="sponsor-logo-placeholder"><i class="fas fa-image"></i></div>
                    <?php endif; ?>

                    <div class="sponsor-card-info">
                        <h3><?= htmlspecialchars($s->nombre) ?></h3>
                        <div class="sponsor-social-icons">
                            <?php if ($s->sitio_web): ?>
                                <a href="<?= htmlspecialchars($s->sitio_web) ?>" target="_blank" rel="noopener" title="Sitio web" class="ssi ssi-web"><i class="fas fa-globe"></i></a>
                            <?php endif; ?>
                            <?php if ($s->instagram): ?>
                                <?php $ig_url = (strpos($s->instagram, 'http') === 0) ? $s->instagram : 'https://instagram.com/' . ltrim($s->instagram, '@'); ?>
                                <a href="<?= htmlspecialchars($ig_url) ?>" target="_blank" rel="noopener" title="Instagram" class="ssi ssi-ig"><i class="fab fa-instagram"></i></a>
                            <?php endif; ?>
                            <?php if ($s->facebook): ?>
                                <a href="<?= htmlspecialchars($s->facebook) ?>" target="_blank" rel="noopener" title="Facebook" class="ssi ssi-fb"><i class="fab fa-facebook"></i></a>
                            <?php endif; ?>
                            <?php if ($s->whatsapp): ?>
                                <a href="https://wa.me/<?= preg_replace('/\D/', '', $s->whatsapp) ?>" target="_blank" rel="noopener" title="WhatsApp" class="ssi ssi-wa"><i class="fab fa-whatsapp"></i></a>
                            <?php endif; ?>
                            <?php if ($s->otro_link): ?>
                                <a href="<?= htmlspecialchars($s->otro_link) ?>" target="_blank" rel="noopener" title="<?= htmlspecialchars($s->otro_label ?: 'Otro') ?>" class="ssi ssi-otro"><i class="fas fa-share-alt"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="sponsor-badges">
                    <span class="badge-sp <?= $s->activo ? 'activo' : 'inactivo' ?>">
                        <?= $s->activo ? 'Activo' : 'Inactivo' ?>
                    </span>
                    <?php if ($s->es_global): ?>
                        <span class="badge-sp global"><i class="fas fa-globe"></i> Global</span>
                    <?php endif; ?>
                    <?php if ($s->torneo_id): ?>
                        <span class="badge-sp torneo"><i class="fas fa-trophy"></i> <?= htmlspecialchars($s->torneo_nombre ?? 'Torneo') ?></span>
                    <?php endif; ?>
                    <span class="badge-sp orden">Orden: <?= (int)$s->orden ?></span>
                </div>

                <div class="sponsor-card-actions">
                    <a href="<?= base_url('admin/Sponsors/editar/' . $s->id) ?>" class="btn-sp editar">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="<?= base_url('admin/Sponsors/toggle/' . $s->id) ?>"
                       class="btn-sp toggle"
                       onclick="return confirm('¿<?= $s->activo ? 'Desactivar' : 'Activar' ?> este sponsor?')">
                        <i class="fas fa-<?= $s->activo ? 'eye-slash' : 'eye' ?>"></i>
                        <?= $s->activo ? 'Desactivar' : 'Activar' ?>
                    </a>
                    <a href="<?= base_url('admin/Sponsors/eliminar/' . $s->id) ?>"
                       class="btn-sp eliminar"
                       onclick="return confirm('¿Eliminar el sponsor <?= htmlspecialchars(addslashes($s->nombre)) ?>? Esta acción no se puede deshacer.')">
                        <i class="fas fa-trash"></i> Eliminar
                    </a>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
