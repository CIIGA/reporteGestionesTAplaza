<?php
// Determina si la conexión es HTTP o HTTPS
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Obtiene el nombre del host
$host = $_SERVER['HTTP_HOST'];

// Construye la URL base
$baseUrl = $protocol . $host;
?>

<nav class="navbar sticky-bottom navbar-expand-lg">
    <span class="navbar-text" style="font-size:12px;font-weigth:normal;color: #7a7a7a;">
        Implementta ©<br>
        Estrategas de México <i class="far fa-registered"></i><br>
        Centro de Inteligencia Informática y Geografía Aplicada CIIGA
        <hr style="width:105%;border-color:#7a7a7a;">
        Created and designed by © <?php echo date('Y') ?> Estrategas de México<br>
    </span><hr>
    <span class="navbar-text" style="font-size:12px;font-weigth:normal;color: #7a7a7a;">
        Contacto:<br>
        <i class="fas fa-phone-alt"></i> Red: 187<br>
        <i class="fas fa-phone-alt"></i> 66 4120 1451<br>
        <i class="fas fa-envelope"></i> sistemas@estrategas.mx<br>
    </span>
    <ul class="navbar-nav mr-auto">
        <br><br><br><br><br><br><br><br>
    </ul>
    <form class="form-inline my-2 my-lg-0">
        <a href="<?= $baseUrl ?>/new_implementta/"><img src="<?= $baseUrl ?>/new_implementta/modulos/img/logoImplementta.png" width="155" height="150" alt=""></a>
        <a href="http://estrategas.mx/" target="_blank"><img src="<?= $baseUrl ?>/new_implementta/modulos/img/logoTop.png" width="200" height="85" alt=""></a>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </form>
</nav>