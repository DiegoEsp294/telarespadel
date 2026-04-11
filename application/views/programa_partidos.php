<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Programa — <?= htmlspecialchars($torneo->nombre) ?></title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  background: #e8edf2;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  padding: 16px;
  color: #444;
}

/* ── ACCIONES (no se capturan) ── */
.prog-acciones {
  display: flex; gap: 10px; justify-content: center;
  margin-bottom: 10px; flex-wrap: wrap; align-items: center;
}
.btn-prog {
  padding: 10px 22px; border: none; border-radius: 6px;
  font-size: 13px; font-weight: 700; cursor: pointer; display: flex;
  align-items: center; gap: 6px;
}
.btn-prog.descargar { background: #003366; color: #fff; }
.btn-prog.compartir { background: #25D366; color: #fff; }

.prog-filtros {
  display: flex; gap: 6px; justify-content: center;
  flex-wrap: wrap; margin-bottom: 16px;
}
.btn-filtro-dia {
  padding: 6px 14px; border: 2px solid #003366; border-radius: 20px;
  font-size: 12px; font-weight: 700; cursor: pointer; background: #fff; color: #003366;
  transition: background .15s, color .15s;
}
.btn-filtro-dia.active { background: #003366; color: #fff; }
.btn-filtro-dia.naranja { border-color: #FF6600; color: #FF6600; }
.btn-filtro-dia.naranja.active { background: #FF6600; color: #fff; }

/* ── POSTER ── */
#programa-wrap {
  max-width: 900px;
  margin: 0 auto;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 24px rgba(0,0,0,.12);
}

/* HEADER */
.prog-header {
  background: #003366;
  color: #fff;
  padding: 20px 24px 18px;
  display: flex;
  align-items: center;
  gap: 16px;
}
.prog-header img {
  height: 60px;
  width: auto;
  flex-shrink: 0;
  border-radius: 6px;
  background: #fff;
  padding: 4px;
}
.prog-header-info { flex: 1; min-width: 0; }
.prog-club {
  font-size: 10px;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: #FF6600;
  font-weight: 700;
  margin-bottom: 4px;
}
.prog-titulo {
  font-size: 20px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: .5px;
  line-height: 1.2;
}
.prog-fechas-label {
  font-size: 11px;
  color: rgba(255,255,255,.65);
  margin-top: 5px;
}

/* CUERPO */
.prog-body {
  padding: 16px 16px 20px;
  background: #f5f7fa;
}

/* DÍA */
.prog-dia-bloque { margin-bottom: 14px; }
.prog-dia-titulo {
  background: #003366;
  color: #fff;
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 2px;
  padding: 5px 12px;
  border-radius: 5px;
  margin-bottom: 8px;
  display: inline-block;
}
.prog-dia-titulo.sin { background: #aaa; }

/* GRID 2 COLUMNAS */
.prog-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 5px;
}
.prog-grid.una-col { grid-template-columns: 1fr; }

/* TARJETA */
.prog-partido {
  display: flex;
  align-items: stretch;
  background: #fff;
  border-radius: 7px;
  overflow: hidden;
  border: 1px solid #e0e4ea;
  border-left: 4px solid #003366;
}
.prog-partido.sin-hora { border-left-color: #ccc; opacity: .75; }

.prog-tiempo {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center;
  min-width: 46px; padding: 6px 4px;
  background: #003366;
  text-align: center;
}
.prog-hora {
  font-size: 12px; font-weight: 800; color: #fff; line-height: 1;
}
.prog-cancha {
  font-size: 8px; color: #FF6600; font-weight: 700;
  margin-top: 3px; text-transform: uppercase; letter-spacing: .5px;
}

.prog-cuerpo {
  flex: 1; padding: 5px 8px; min-width: 0;
}
.prog-tags {
  display: flex; gap: 4px; margin-bottom: 3px; flex-wrap: wrap;
}
.prog-tag {
  font-size: 8px; font-weight: 800; text-transform: uppercase;
  letter-spacing: .4px; padding: 1px 5px; border-radius: 3px;
}
.prog-tag.zona { background: #e8edf5; color: #003366; }
.prog-tag.cat  { background: #fff3eb; color: #FF6600; }

.prog-vs {
  display: flex; align-items: center; gap: 4px;
  font-size: 10.5px; line-height: 1.3;
}
.prog-pareja {
  flex: 1; font-weight: 700; color: #222;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.prog-sep {
  font-size: 8px; color: #bbb; font-weight: 700; flex-shrink: 0;
}

/* FOOTER */
.prog-footer {
  background: #003366;
  text-align: center;
  padding: 10px;
  font-size: 10px;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: rgba(255,255,255,.5);
}
.prog-footer span { color: #FF6600; font-weight: 700; }

@media print { .prog-acciones { display: none; } body { padding: 0; } }
</style>
</head>
<body>

<div class="prog-acciones">
  <button class="btn-prog descargar" onclick="descargar()">⬇ Descargar</button>
  <button class="btn-prog compartir" onclick="compartir()">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.533 5.86L.057 23.999l6.305-1.654A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.007-1.373l-.36-.214-3.733.979 1-3.642-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
    Compartir
  </button>
</div>

<?php
  // Recolectar fechas únicas para los botones de filtro
  $fechas_filtro = [];
  foreach ($todos_partidos as $p) {
      if ($p->fecha) $fechas_filtro[substr($p->fecha, 0, 10)] = true;
  }
  ksort($fechas_filtro);
  $dias_es_btn = ['Sunday'=>'Dom','Monday'=>'Lun','Tuesday'=>'Mar','Wednesday'=>'Mié','Thursday'=>'Jue','Friday'=>'Vie','Saturday'=>'Sáb'];
?>
<div class="prog-filtros">
  <button class="btn-filtro-dia active" data-filtro="">Todos los días</button>
  <?php foreach (array_keys($fechas_filtro) as $i => $f): ?>
    <button class="btn-filtro-dia<?= $i % 2 ? ' naranja' : '' ?>" data-filtro="<?= $f ?>">
      <?= ($dias_es_btn[date('l', strtotime($f))] ?? '') . ' ' . date('d/m', strtotime($f)) ?>
    </button>
  <?php endforeach; ?>
</div>

<div id="programa-wrap">

  <!-- HEADER -->
  <div class="prog-header">
    <img src="<?= base_url('assets/images/logo.png') ?>" alt="Telares Padel" crossorigin="anonymous">
    <div class="prog-header-info">
      <div class="prog-club">Telares Padel</div>
      <div class="prog-titulo"><?= htmlspecialchars($torneo->nombre) ?></div>
      <?php
        $fechas_torneo = [];
        foreach ($todos_partidos as $p) {
            if ($p->fecha) $fechas_torneo[substr($p->fecha, 0, 10)] = true;
        }
        ksort($fechas_torneo);
        $dias_es = ['Sunday'=>'Dom','Monday'=>'Lun','Tuesday'=>'Mar','Wednesday'=>'Mié','Thursday'=>'Jue','Friday'=>'Vie','Saturday'=>'Sáb'];
        if (!empty($fechas_torneo)) {
            $label = implode(' · ', array_map(fn($f) =>
                ($dias_es[date('l', strtotime($f))] ?? '') . ' ' . date('d/m', strtotime($f)),
                array_keys($fechas_torneo)
            ));
            echo '<div class="prog-fechas-label">' . $label . '</div>';
        }
      ?>
    </div>
  </div>

  <!-- CUERPO -->
  <div class="prog-body">
  <?php
    $dias_es_full = ['Sunday'=>'Domingo','Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado'];
    $zona_labels  = [1=>'Cuartos', 2=>'Semifinal', 3=>'Final'];

    $con_fecha = [];
    $sin_fecha = [];
    foreach ($todos_partidos as $p) {
        if (!$p->pareja1_nombre && !$p->pareja2_nombre) continue;
        if ($p->fecha) $con_fecha[substr($p->fecha, 0, 10)][] = $p;
        else           $sin_fecha[] = $p;
    }
    ksort($con_fecha);

    foreach ($con_fecha as $fecha => $partidos):
        $dia_en    = date('l', strtotime($fecha));
        $dia_label = ($dias_es_full[$dia_en] ?? $dia_en) . ' ' . date('d/m', strtotime($fecha));
        $dos_col   = count($partidos) > 6;
  ?>
    <div class="prog-dia-bloque" data-fecha="<?= $fecha ?>">
      <div class="prog-dia-titulo"><?= $dia_label ?></div>
      <div class="prog-grid<?= $dos_col ? '' : ' una-col' ?>">
      <?php foreach ($partidos as $p):
          $hora = $p->hora ? substr($p->hora, 0, 5) : null;
          $zona_label = $p->zona_numero
              ? 'Zona ' . chr(64 + $p->zona_numero)
              : ($zona_labels[$p->ronda] ?? 'Playoff');
      ?>
        <div class="prog-partido<?= $hora ? '' : ' sin-hora' ?>">
          <div class="prog-tiempo">
            <div class="prog-hora"><?= $hora ?? '-' ?></div>
            <?php if ($p->cancha): ?>
              <div class="prog-cancha">C<?= $p->cancha ?></div>
            <?php endif; ?>
          </div>
          <div class="prog-cuerpo">
            <div class="prog-tags">
              <span class="prog-tag zona"><?= $zona_label ?></span>
              <?php if ($p->categoria_nombre): ?>
                <span class="prog-tag cat"><?= htmlspecialchars($p->categoria_nombre) ?></span>
              <?php endif; ?>
            </div>
            <div class="prog-vs">
              <span class="prog-pareja"><?= htmlspecialchars($p->pareja1_nombre ?? '?') ?></span>
              <span class="prog-sep">VS</span>
              <span class="prog-pareja" style="text-align:right"><?= htmlspecialchars($p->pareja2_nombre ?? '?') ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if (!empty($sin_fecha)): ?>
    <div class="prog-dia-bloque">
      <div class="prog-dia-titulo sin">Sin horario asignado</div>
      <div class="prog-grid<?= count($sin_fecha) > 6 ? '' : ' una-col' ?>">
      <?php foreach ($sin_fecha as $p):
          $zona_label = $p->zona_numero
              ? 'Zona ' . chr(64 + $p->zona_numero)
              : ($zona_labels[$p->ronda] ?? 'Playoff');
      ?>
        <div class="prog-partido sin-hora">
          <div class="prog-tiempo"><div class="prog-hora">-</div></div>
          <div class="prog-cuerpo">
            <div class="prog-tags">
              <span class="prog-tag zona"><?= $zona_label ?></span>
              <?php if ($p->categoria_nombre): ?>
                <span class="prog-tag cat"><?= htmlspecialchars($p->categoria_nombre) ?></span>
              <?php endif; ?>
            </div>
            <div class="prog-vs">
              <span class="prog-pareja"><?= htmlspecialchars($p->pareja1_nombre ?? '?') ?></span>
              <span class="prog-sep">VS</span>
              <span class="prog-pareja" style="text-align:right"><?= htmlspecialchars($p->pareja2_nombre ?? '?') ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
  </div>

  <div class="prog-footer"><span>telarespadel.com.ar</span></div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
var filtroActivo = '';

// Filtro de días
document.querySelectorAll('.btn-filtro-dia').forEach(function(btn) {
    btn.addEventListener('click', function() {
        filtroActivo = this.dataset.filtro;
        document.querySelectorAll('.btn-filtro-dia').forEach(function(b) {
            b.classList.remove('active');
        });
        this.classList.add('active');

        document.querySelectorAll('.prog-dia-bloque').forEach(function(bloque) {
            if (!filtroActivo || bloque.dataset.fecha === filtroActivo) {
                bloque.style.display = '';
            } else {
                bloque.style.display = 'none';
            }
        });
    });
});

function capturar(callback) {
    var el = document.getElementById('programa-wrap');
    html2canvas(el, {
        backgroundColor: '#fff',
        scale: 2,
        useCORS: true,
        allowTaint: false,
        logging: false
    }).then(callback);
}

function nombreArchivo() {
    var base = 'programa-<?= preg_replace('/[^a-z0-9]+/', '-', strtolower($torneo->nombre)) ?>';
    var btn = document.querySelector('.btn-filtro-dia.active');
    if (btn && btn.dataset.filtro) {
        base += '-' + btn.dataset.filtro;
    }
    return base + '.png';
}

function descargar() {
    capturar(function(canvas) {
        var a = document.createElement('a');
        a.download = nombreArchivo();
        a.href = canvas.toDataURL('image/png');
        a.click();
    });
}

function compartir() {
    capturar(function(canvas) {
        canvas.toBlob(function(blob) {
            var file = new File([blob], nombreArchivo(), { type: 'image/png' });
            if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
                navigator.share({ title: '<?= addslashes(htmlspecialchars($torneo->nombre)) ?>', files: [file] }).catch(function(){});
            } else {
                var a = document.createElement('a');
                a.download = nombreArchivo();
                a.href = canvas.toDataURL('image/png');
                a.click();
            }
        });
    });
}
</script>
</body>
</html>
