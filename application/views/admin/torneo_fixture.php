<div class="">
    <div class="">

        <div class="container">

            <!-- =============================
                SELECTOR CATEGORIA
            ============================= -->

            <form method="get">
                <select name="categoria_id" class="select-categoria"
                        onchange="this.form.submit()">

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
                <button class="tab active" onclick="showTab('zonas')">Zonas</button>
                <button class="tab" onclick="showTab('playoff')">Playoff</button>
            </div>

            <!-- =============================
                    ZONAS
            ============================= -->

            <div id="tab-zonas" class="fixture-tab-content">

                <div class="fixture-container">

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
                                        <div class="numero">
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
                                    <div>SETS</div>

                                </div>

                                <?php foreach($zona['partidos'] as $partido): ?>

                                    <div class="duelo-row"
                                        data-partido-id="<?= $partido['partido_id'] ?>"
                                        onclick="abrirModalPartido(this)">

                                        <div><?= $partido['duelo'] ?></div>
                                        <div><?= strtoupper($partido['dia']) ?></div>
                                        <div class="hora"><?= $partido['hora'] ?></div>
                                        <div class="cancha"><?= $partido['cancha'] ?></div>
                                        <div class="sets">
                                            <?php 
                                            $sets = [];?>
                                            <?= $partido['set1_p1'] ?? '-' ?>-<?= $partido['set1_p2'] ?? '-' ?>
                                            <?= $partido['set2_p1'] ?? '-' ?>-<?= $partido['set2_p2'] ?? '-' ?>
                                            <?= $partido['set3_p1'] ?? '-' ?>-<?= $partido['set3_p2'] ?? '-' ?>
                                            <?php  echo implode(', ', $sets); 
                                            ?>
                                        </div>
                                    </div>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

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

<div id="modalPartido" class="modal" style="display:none;">

    <div class="modal-content">

        <h3>Editar Partido</h3>

        <form id="formPartido">

            <input type="hidden" name="id" id="partido_id">

            <label>Día</label>
            <input type="date" name="dia" id="dia">

            <label>Hora</label>
            <input type="time" name="hora" id="hora" step="1800">

            <label>Cancha</label>
            <input type="text" name="cancha" id="cancha">

            <label>Primer set</label>
            <input type="text" name="set_1" id="set_1" placeholder="N-N" pattern="^\d{1,2}-\d{1,2}$" >

            <label>Segundo set</label>
            <input type="text" name="set_2" id="set_2" placeholder="N-N" pattern="^\d{1,2}-\d{1,2}$">

            <label>Tercar set</label>
            <input type="text" name="set_3" id="set_3" placeholder="N-N" pattern="^\d{1,2}-\d{1,2}$">

            <button type="submit">Guardar</button>
            <button type="button" onclick="cerrarModal()">Cancelar</button>

        </form>

    </div>
</div>



<script>
function abrirModalPartido(el)
{
    const partidoId = el.dataset.partidoId;

    fetch("<?= base_url('admin/torneos/obtener_partido') ?>/"+partidoId)
        .then(r => r.json())
        .then(data => {

            document.getElementById('partido_id').value = data.id;
            // para el input type="date"
            let fechaCompleta = data.fecha;

            if (fechaCompleta) {
                let diaInput = fechaCompleta.slice(0, 10);
                document.getElementById('dia').value = diaInput;
            } else {
                document.getElementById('dia').value = '';
            }

            document.getElementById('hora').value = data.hora;
            document.getElementById('cancha').value = data.cancha;

            document.getElementById('modalPartido').style.display='flex';
        });
}

function cerrarModal()
{
    document.getElementById('modalPartido').style.display='none';
}
</script>

<script>
document.getElementById('formPartido')
.addEventListener('submit', function(e){

    e.preventDefault();

    const formData = new FormData(this);

    fetch("<?= base_url('admin/torneos/actualizar_partido') ?>", {
        method: "POST",
        body: formData
    })
    .then(r => r.json())
    .then(resp => {
        cerrarModal();
        location.reload();
    });

});
</script>


<script>
function showTab(tab)
{
    document.querySelectorAll('.fixture-tab-content')
        .forEach(el => el.style.display='none');

    document.getElementById('tab-'+tab).style.display='block';

    document.querySelectorAll('.tab')
        .forEach(t=>t.classList.remove('active'));

    event.target.classList.add('active');
}

</script>