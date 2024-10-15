<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tu Título</title>
  <!-- Incluye la biblioteca SweetAlert aquí -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

  <?php
  session_start();

  if (isset($_POST['login'])) {
    require "conexion/cnx.php";

    $correo = $_POST['correo'];
    $clave = $_POST['pass'];

    $sql = "SELECT * FROM usuario WHERE clave=? AND correo=?";
    $params = array($clave, $correo);

    $consulta = sqlsrv_query($cnx, $sql, $params);

    if (sqlsrv_has_rows($consulta)) {
      $datos = sqlsrv_fetch_array($consulta);
      $_SESSION['user'] = $datos['id_usuario'];
      $_SESSION['id_rol'] = $datos['id_rol'];
      $_SESSION['correo'] = $datos['correo'];



      $nomUsr = $datos['nombre'];


      if ($_SESSION['id_rol'] == 1) {
        $archivo = 'mesaAyuda/mesaAyuda.php';
      } elseif ($_SESSION['id_rol'] == 2) {
        $archivo = 'lider/liderCasos.php';
      } elseif ($_SESSION['id_rol'] == 5) {
        $archivo = 'cliente/cliente.php';
      } elseif ($_SESSION['id_rol'] == 3) {
        $archivo = 'colaborador/tareasColaborador.php';
      } else {
        # code...
      }
      echo "<script>
                let timerInterval
                Swal.fire({
                  title: 'Iniciando sesión ',
                  html: 'Bienvenido a ticketWeb <br>$nomUsr',
                  icon: 'success',
                  timer: 2000,
                  timerProgressBar: true,
                  didOpen: () => {
                    Swal.showLoading()
                    const b = Swal.getHtmlContainer().querySelector('b')
                    timerInterval = setInterval(() => {
                      b.textContent = Swal.getTimerLeft()
                    }, 100)
                  },
                  willClose: () => {
                    clearInterval(timerInterval)
                  }
                }).then((result) => {
                  if (result.dismiss === Swal.DismissReason.timer) {
                    console.log('I was closed by the timer')
                  }
                })
            </script>";
      echo '<meta http-equiv="refresh" content="2;url=' . $archivo . '">';
    } else {
      $_SESSION['login_error'] = 'Los datos de acceso no existen en ticketWeb';
      header("Location: login.php");
      exit();
    }
  } else {
    header("Location: login.php");
    exit();
  }
  ?>

</body>

</html>