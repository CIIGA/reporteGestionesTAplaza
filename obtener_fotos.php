<?php
session_start();
require "cnx/cnx.php";
$cnx = conexion('implementtaTecateA');


$fecha = $_GET['fecha'];
$cuenta = $_GET['cuenta'];


$validar = true;


$sql_cruce = "SELECT a.IdRegistroReductores AS id,fechaCaptura as fecha,IdAspUser as id_user, idTarea as tarea FROM RegistroReductores a
                  WHERE Cuenta='$cuenta' AND CONVERT(DATE, a.fechacaptura) = '$fecha'";

$cnx_sql_cruce = sqlsrv_query($cnx, $sql_cruce);
$cruce = sqlsrv_fetch_array($cnx_sql_cruce);
$id_gestion = $cruce['id'];
$g_fecha = $cruce['fecha'];

$fechaFormatted = (new DateTime($fecha))->format('Y-m-d');
$g_user = $cruce['id_user'];
$g_tarea = $cruce['tarea'];

$sql_fotos = "SELECT f.idRegistroFoto, f.cuenta, f.idAspUser, f.nombreFoto, f.idTarea, 
                         f.fechaSincronizacion, f.tipo, f.urlImagen, f.fechaCaptura 
                  FROM RegistroReductores a
                  INNER JOIN [dbo].Registrofotomovil f ON a.cuenta = f.cuenta 
                  WHERE a.IdRegistroReductores = '$id_gestion'
                  AND CONVERT(DATE, a.fechacaptura) = CONVERT(DATE, f.fechacaptura)";

$cnx_sql_fotos = sqlsrv_query($cnx, $sql_fotos);
$count_fotos = 0;
// Construir un array de fotos
$fotos = [];
if (sqlsrv_has_rows($cnx_sql_fotos)) {
    while ($foto = sqlsrv_fetch_array($cnx_sql_fotos, SQLSRV_FETCH_ASSOC)) {
        $fotos[] = [
            'url' => htmlspecialchars($foto['urlImagen']),
            'titulo' => htmlspecialchars(utf8_encode($foto['tipo'])),
            'id' => htmlspecialchars($foto['idRegistroFoto']),
            'cuenta' => htmlspecialchars($foto['cuenta'])
        ];
        $count_fotos += 1;
    }
}

// Construir el HTML
?>
<style>
    /* Personaliza el botón para que no ocupe todo el ancho */
    .btn-normal {
        display: inline-block;
        margin: 0;
        padding: 0.375rem 0.75rem;
        /* Tamaño de padding del botón */
    }
</style>

<div class="row">
    <div class="col-md-8">
        <h4>Cuenta: <?php echo htmlspecialchars($cuenta); ?></h4>
        <span class="badge bg-warning text-dark" style="font-size: 11px;"><?php echo $count_fotos; ?> fotos de la gestión</span>
    </div>
    
</div>





<div class="row">
    <?php if (!empty($fotos)) : ?>
        <?php foreach ($fotos as $foto) : ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <!-- Enlace alrededor de la imagen con target="_blank" -->
                    <a href="<?php echo $foto['url']; ?>" target="_blank">
                        <img src="<?php echo $foto['url']; ?>" class="card-img-top" alt="Foto" style="object-fit: cover; width: 100%; height: 100px" >
                    </a>
                    <div class="card-footer">
                        <p class="card-title" style="text-align: center; text-decoration: underline;">
                            <?php echo $foto['titulo']; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="col-12">
            <p>No se encontraron fotos.</p>
        </div>
    <?php endif; ?>
</div>


<!-- Incluye SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Capturar el evento de clic sobre las imágenes
    // document.querySelectorAll('.card-img-top').forEach(img => {
    //     img.addEventListener('click', function () {
    //         const imageUrl = this.getAttribute('data-url');
    //         const imageTitle = this.getAttribute('data-titulo');
            
    //         // SweetAlert con solo la imagen
    //         Swal.fire({
    //             title: imageTitle, // Título opcional
    //             imageUrl: imageUrl,
    //             imageWidth: '100%',
    //             imageAlt: 'Imagen',
    //             showConfirmButton: false, // Para no mostrar el botón
    //             allowOutsideClick: true, // Cerrar al hacer clic fuera
    //         });
    //     });
    // });
</script>



