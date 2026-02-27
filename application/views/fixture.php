<div class="">
    <div class="">

        <div >
            <form method="get" style="width: 100%;">
                <select name="categoria_id" class="select-categoria"
                        onchange="this.form.submit()"
                        style="width: 100%; padding: 8px 12px; box-sizing: border-box;">
                    <?php foreach($categorias as $cat): ?>
                        <option value="<?= $cat->id ?>"
                            <?= $categoria_id == $cat->id ? 'selected':'' ?>>
                            <?= $cat->nombre ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <hr>

            <!-- =============================
                    TABS
            ============================= -->

            <div class="fixture-tabs">
                <button class="tab active" onclick="showTab(event,'zonas')">Zonas</button>
                <button class="tab" onclick="showTab(event,'resultados')">Resultados</button>
                <!-- <button class="tab" onclick="showTab('playoff')">Playoff</button> -->
            </div>

            <!-- =============================
                    ZONAS
            ============================= -->

            <div id="tab-zonas" class="fixture-tab-content">

                <div class="">

                    <?php foreach($zonas as $zona): ?>

                        <div class="zona-card">

                            <!-- HEADER -->
                            <div class="zona-header">
                                <h2>FASE DE GRUPOS</h2>
                            </div>

                            <!-- GRUPO -->
                            <div class="grupo-title">
                                GRUPO <?= $zona['grupo'] ?>
                            </div>

                            <!-- PAREJAS -->
                            <div class="parejas-box">

                                <div class="parejas-header">
                                    <div></div>
                                    <div>PAREJA</div>
                                </div>

                                <?php foreach($zona['parejas'] as $pareja): ?>

                                    <div class="pareja-row">
                                        <div class="nombre">
                                            <?= $pareja['numero'] ?>
                                        </div>

                                        <div class="nombre">
                                            <?= strtoupper($pareja['nombre']) ?>
                                        </div>
                                    </div>

                                <?php endforeach; ?>

                            </div>

                            <!-- DUELOS -->
                            <div class="duelos-box">

                                <div class="duelos-header">
                                    <div>DUELO</div>
                                    <div>DIA</div>
                                    <div>HORA</div>
                                    <div>CANCHA</div>
                                    <!-- <div>SETS</div> -->

                                </div>

                                <?php foreach($zona['partidos'] as $partido): ?>

                                    <div class="duelo-row"
                                        data-partido-id="<?= $partido['partido_id'] ?>">

                                        <div><?= $partido['duelo'] ?></div>
                                        <div><?= strtoupper($partido['dia']) ?></div>
                                        <div class="hora"><?= $partido['hora'] ?></div>
                                        <div class="cancha"><?= $partido['cancha'] ?></div>
                                        <!-- <div class="sets">
                                            <?php 
                                            $sets = [];?>
                                            <?= $partido['set1_p1'] ?? '-' ?>-<?= $partido['set1_p2'] ?? '-' ?>
                                            <?= $partido['set2_p1'] ?? '-' ?>-<?= $partido['set2_p2'] ?? '-' ?>
                                            <?= $partido['set3_p1'] ?? '-' ?>-<?= $partido['set3_p2'] ?? '-' ?>
                                            <?php  echo implode(', ', $sets); 
                                            ?>
                                        </div> -->
                                    </div>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

            <div id="tab-resultados" class="fixture-tab-content">

                <div class="resultados-wrapper container">
                    <div class="">
                        <?php foreach($zonas as $zona): ?>

                            <div class="resultados-card">

                                <div class="resultados-header">
                                    <h3>Grupo <?= $zona['grupo'] ?></h3>
                                </div>

                                <?php
                                $parejas_map = [];
                                foreach($zona['parejas'] as $p){
                                    $parejas_map[$p['numero']] = strtoupper($p['nombre']);
                                }
                                ?>

                                <div class="resultados-body">

                                    <?php foreach($zona['partidos'] as $partido): ?>

                                        <?php
                                        list($p1,$p2) = explode(' VS ', $partido['duelo']);

                                        $nombre1 = $parejas_map[$p1] ?? '';
                                        $nombre2 = $parejas_map[$p2] ?? '';

                                        $jugado = $partido['set1_p1'] !== null;
                                        ?>

                                        <div class="resultado-item">

                                            <div class="resultado-meta">
                                                <span><?= $partido['dia'] ?></span>
                                                <span><?= $partido['hora'] ?></span>
                                                <span>Cancha <?= $partido['cancha'] ?? '-' ?></span>
                                            </div>

                                            <div class="resultado-match">

                                                <!-- Pareja 1 -->
                                                <div class="pareja-block">
                                                    <div class="pareja-nombre"><?= $nombre1 ?></div>
                                                    <div class="sets">
                                                        <?php if($jugado): ?>
                                                            <span><?= $partido['set1_p1'] ?>-<?= $partido['set1_p2'] ?></span>
                                                            <?php if($partido['set2_p1'] !== null): ?>
                                                                <span><?= $partido['set2_p1'] ?>-<?= $partido['set2_p2'] ?></span>
                                                            <?php endif; ?>
                                                            <?php if($partido['set3_p1'] !== null): ?>
                                                                <span><?= $partido['set3_p1'] ?>-<?= $partido['set3_p2'] ?></span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="estado-pendiente">Pendiente</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Pareja 2 -->
                                                <div class="pareja-block">
                                                    <div class="pareja-nombre"><?= $nombre2 ?></div>
                                                    <!-- <div class="sets">
                                                        <?php if($jugado): ?>
                                                            <span><?= $partido['set1_p2'] ?>-<?= $partido['set1_p1'] ?></span>
                                                            <?php if($partido['set2_p1'] !== null): ?>
                                                                <span><?= $partido['set2_p2'] ?>-<?= $partido['set2_p1'] ?></span>
                                                            <?php endif; ?>
                                                            <?php if($partido['set3_p1'] !== null): ?>
                                                                <span><?= $partido['set3_p2'] ?>-<?= $partido['set3_p1'] ?></span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="estado-pendiente">Pendiente</span>
                                                        <?php endif; ?>
                                                    </div> -->
                                                </div>

                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                </div>

                            </div>

                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
            <!-- =============================
                    PLAYOFF
            ============================= -->

            <div id="tab-playoff" class="fixture-tab-content" style="display:none;">
                <h3>Playoff</h3>

                <?php for ($i = 0; $i < count($playoff); $i += 2): ?>

                    <?php
                        $pareja1 = $playoff[$i];
                        $pareja2 = $playoff[$i + 1] ?? null;
                    ?>

                    <div class="match-card">

                        <div class="match-info">

                            <div class="pareja">
                                <?= $pareja1->pareja_nombre ?>
                                <small>
                                    (<?= $pareja1->posicion ?>° Zona <?= $pareja1->zona_numero ?>)
                                </small>
                            </div>

                            <div class="vs">VS</div>

                            <div class="pareja">
                                <?= $pareja2
                                    ? $pareja2->pareja_nombre .
                                    " <small>({$pareja2->posicion}° Zona {$pareja2->zona_numero})</small>"
                                    : "Libre"
                                ?>
                            </div>

                        </div>

                    </div>

                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>


<script>
document.getElementById('formPartido')
.addEventListener('submit', function(e){

    e.preventDefault();

    const formData = new FormData(this);

    fetch("<?= base_url('admin/partidos/actualizar') ?>", {
        method: "POST",
        body: formData
    })
    .then(r => r.json())
    .then(resp => {

        if(resp.ok)
        {
            cerrarModal();

            // opción simple
            location.reload();

            // después podemos hacerlo realtime sin reload
        }
    });

});
</script>


<script>
function showTab(e, tab)
{
    // ocultar todos
    document.querySelectorAll('.fixture-tab-content')
        .forEach(el => el.style.display = 'none');

    // mostrar seleccionado
    document.getElementById('tab-' + tab).style.display = 'block';

    // quitar active
    document.querySelectorAll('.tab')
        .forEach(t => t.classList.remove('active'));

    // activar botón clickeado
    e.currentTarget.classList.add('active');
}
</script>