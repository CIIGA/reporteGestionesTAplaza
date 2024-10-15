<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión</title>
  <link rel="icon" href="./icono/implementtaIcon.png">
  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">  <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css" id="theme-styles">
  <style>
    body {
      background-image: url(img/back.jpg);
      background-repeat: repeat;
      background-size: 100%;
      background-attachment: fixed;
      overflow-x: hidden;
      /* ocultar scrolBar horizontal*/
    }

    body {
      font-family: sans-serif;
      font-style: normal;
      font-weight: normal;
      width: 100%;
      height: 100%;
      margin-top: -2%;
      padding-top: 0px;
    }

    .jumbotron {
      margin-top: 0%;
      margin-bottom: 0%;
      padding-top: 4%;
      padding-bottom: 1%;
    }

    .padding {
      padding-right: 15%;
      padding-left: 15%;
    }
  </style>
  <?php // require "include/nav.php"; 
  ?>
</head>

<body>
  <?php
  if (isset($_POST['login'])) {
    $usuario = $_POST['correo'];
    $password = $_POST['pass'];


    if ($usuario == 'gerencia.tecate.agua@erdm.mx' && $password == 'adm1n*24') {

      $_SESSION['user'] = 1;


    
      echo "<script>
                let timerInterval
                Swal.fire({
                  title: 'Iniciando sesión ',
                  icon: 'success',
                  timer: 1000,
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
                  /* Read more about handling dismissals below */
                  if (result.dismiss === Swal.DismissReason.timer) {
                    console.log('I was closed by the timer')
                  }
                })
            </script>";


        echo '<meta http-equiv="refresh" content="1,url=./">';
     
    } else {
      echo "<script>
                let timerInterval
                Swal.fire({
                  title: '¡Error!',
                  html: 'Los datos de acceso no existen en Implementta <br>Intenta nuevamente.',
                  icon: 'error',
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
                  /* Read more about handling dismissals below */
                  if (result.dismiss === Swal.DismissReason.timer) {
                    console.log('I was closed by the timer')
                  }
                })
            </script>";
      echo '<meta http-equiv="refresh" content="2,url=login.php">';
    }
  }
  ?>
  <br>
  <div class="jumbotron">
    <div class="container">
      <form action="" method="post">
        <br><br><br><br>
        <div class="row">
          <div class="col-sm-6" data-aos="fade-right" data-aos-duration="1200">
            <div style="text-align:center;">
              <hr>
              <a href="index.php"><img src="img/logoImplementta.png" class="img-fluid" alt="Responsive image" width="60%"></a>
              <hr>
            </div>
          </div>
          <div class="col-sm-6">
            <br>
            <form class="form-inline" method="post">
              <div class="card" style="width:22rem;margin:auto;box-shadow: 0px 0px 7px #717171;">
                <div class="card-body">
                  <div class="input-group mb-3">
                    <input type="email" name="correo" class="form-control form-control-lg" placeholder="Correo electrónico" aria-label="Username" aria-describedby="basic-addon1" required autofocus>
                  </div>
                  <div class="input-group mb-3">
                    <input type="password" name="pass" class="form-control form-control-lg" placeholder="Contraseña" aria-label="Username" aria-describedby="basic-addon1" required>
                  </div>
                  <button type="submit" name="login" class="btn btn-primary btn-lg btn-block" id="botones" data-toggle="tooltip" data-placement="bottom" title="Iniciar Sesion">Iniciar Sesión</button>
                  
                </div>
              </div>
            </form>
          </div>
        </div>
        <br><br><br><br><br><br><br><br><br><br>
      </form>
    </div>
  </div>
  
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
  <!--*************************INICIO FOOTER***********************************************************************-->
  <?php require "footer.php"; ?>
  <!--***********************************FIN FOOTER****************************************************************-->
</body>

</html>