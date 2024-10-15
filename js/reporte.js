$(document).ready(function () {
  // Inicializar el daterangepicker
  $("#rangof").daterangepicker({
    autoUpdateInput: false,
    minDate: "2024-10-01", // Restringe la selección a partir de esta fecha
    locale: {
      cancelLabel: "Limpiar",
      applyLabel: "Aplicar",
      format: "YYYY-MM-DD",
      daysOfWeek: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
      monthNames: [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
      ],
    },
  });

  // Aplicar filtro de fechas
  $("#rangof").on("apply.daterangepicker", function (ev, picker) {
    $(this).val(
      picker.startDate.format("YYYY-MM-DD") +
        " - " +
        picker.endDate.format("YYYY-MM-DD")
    );
  });

  // Limpiar filtro de fechas
  $("#rangof").on("cancel.daterangepicker", function (ev, picker) {
    $(this).val("");
  });

  // Inicializar DataTable con 10 registros por defecto y ocultar la opción de seleccionar registros por página
  var tabla = $("#tblreporte").DataTable({
    scrollX: true, // Habilitar scroll horizontal
    fixedColumns: {
      leftColumns: 2, // Fijar las primeras dos columnas
    },
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
      emptyTable: "No se cuenta con gestiones.",
    },
    dom: "lrtip", // Personaliza el DOM para ocultar el control de longitud de página
    pageLength: 10, // Número de registros por defecto
    lengthChange: false, // Oculta la opción de cambiar el número de registros por página
    columns: [
      {
        data: null,
        render: function (data, type, row) {
          var btnComentarios = `<button class="btn btn-outline-secondary btn-sm comentarios-btn" data-id_gestion="${row.id}" title="Comentarios" style="padding:0%;border:0px; font-size: 1em">
        <img src="https://img.icons8.com/fluency/16/comments.png" /> 
        </button>`;

          var btnFotos = `<button class="btn btn-outline-secondary btn-sm fotos-btn" data-fecha="${row.fecha}" data-cuenta="${row.cuenta}" title="Fotos" style="padding:0%;border:0px; font-size: 1em">
            <img src="https://img.icons8.com/fluency/16/image-gallery.png" /> 
        </button>`;

          var latitud = row.latitud;
          var longitud = row.longitud;
          var geopunto = `https://www.google.com/maps?q=${latitud},${longitud}`;

          var ubi = `
        <a href="${geopunto}" target="_blank" class="btn btn-outline-primary btn-sm" title="Ver gestión" style="padding:0%;border:0px;font-size: 1em">
            <img src="https://img.icons8.com/color/16/google-maps.png"/>
             
        </a>
        `;
          if (row.folio == "") {
            var folio = `<button class="btn btn-outline-secondary btn-sm agregarFolio-btn" data-id_gestion="${row.id}" title="Agregar Folio" style="padding:0%;border:0px; font-size: 1em">
            <img src="https://img.icons8.com/fluency/16/document.png" alt="document"/> 
            </button>`;
          } else {
            var folio = `<button class="btn btn-outline-secondary btn-sm editarFolio-btn" data-id_folio="${row.id_folio}" data-folio="${row.folio}" title="Editar Folio" style="padding:0%;border:0px; font-size: 1em">
            <img src="https://img.icons8.com/fluency/16/pencil-tip.png"/> 
            </button>`;
          }

          return btnComentarios + " " + btnFotos + " " + ubi + " " + folio;
        },
      },
      { data: "cuenta" },
      { data: "folio" },
      { data: "propietario" },
      { data: "domicilio" },
      { data: "gestor" },
      { data: "seriemedidor" },
      {
        data: "fecha",
        render: function (data, type, row) {
          // Verificar si 'data' es un objeto
          if (typeof data === "object") {
            // Obtener la fecha del objeto y convertirla en un objeto Date
            var formattedDate = new Date(data.date);

            // Formatear la fecha como "YYYY-MM-DD"

            // Devolver la fecha formateada
            return formattedDate;
          } else {
            // Si 'data' no es un objeto, devolverlo como está
            return data;
          }
        },
      },
      { data: "lectura" },
      { data: "observaciones" },
    ],
  });

  var filaActual; // Variable para almacenar la fila actual que se está editando

  // Captura el clic del botón "Buscar"
  $("#buscar").on("click", function () {
    cargar_gestiones();
  });

  // Función para cargar las gestiones
  function cargar_gestiones() {
    var rangoFechas = $("#rangof").val();
    var cuenta = $("#cuenta").val();

    // Verificar si ambos campos están vacíos
    if (!rangoFechas && !cuenta) {
      toastr.error("Seleccione un rango de fechas o una cuenta a buscar", "", {
        timeOut: 2000,
        positionClass: "toast-top-right",
        closeButton: true,
        progressBar: true,
      });
      return;
    }

    $.ajax({
      url: "obtenerGestiones.php",
      type: "GET",
      dataType: "json",
      data: {
        rango_fechas: rangoFechas,
        cuenta: cuenta,
      },
      success: function (response) {
        tabla.clear().rows.add(response.data).draw();
        Swal.close();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        Swal.close();
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Error al consultar obtener los datos, de preferencia seleccione un rango de fechas más corto!",
        });
        console.error("Error al obtener las gestiones y cuentas:", errorThrown);
      },
      beforeSend: function () {
        Swal.fire({
          title: "Consultando datos",
          text: "Por favor, espere...",
          allowEscapeKey: false,
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          },
        });
      },
    });
  }

  // agragar folio
  // Evento al hacer clic en el botón con la clase "agregarFolio-btn"
  $(document).on("click", ".agregarFolio-btn", function () {
    // Obtener el id de la gestión desde el atributo data-id_gestion
    var id_gestion = $(this).data("id_gestion");

    // Mostrar el SweetAlert con el formulario
    Swal.fire({
      title: "Agregar Folio",
      html: `<form id="folioForm">
              <input type="hidden" id="id_gestion" value="${id_gestion}">
              <div class="form-group">
                  <label for="folio">Folio</label>
                  <input type="text" id="folio" class="form-control" placeholder="Escribe el folio" required>
              </div>
           </form>`,
      showCancelButton: true,
      confirmButtonText: "Enviar",
      cancelButtonText: "Cancelar",
      preConfirm: () => {
        // Validar que el campo folio no esté vacío
        const folio = Swal.getPopup().querySelector("#folio").value;
        if (!folio) {
          Swal.showValidationMessage("El folio es obligatorio");
        }
        return { folio: folio };
      },
    }).then((result) => {
      if (result.isConfirmed) {
        var folio = result.value.folio;

        // Enviar los datos mediante AJAX
        $.ajax({
          url: "agregarFolio.php", // Cambia 'tu_archivo_php.php' por la ruta de tu archivo PHP
          type: "GET",
          data: {
            id_gestion: id_gestion,
            folio: folio,
          },
          success: function (response) {
            Swal.fire({
              title: "¡Éxito!",
              text: "Folio agregado correctamente",
              icon: "success",
              allowEscapeKey: false, // Evita que se cierre con la tecla Esc
              allowOutsideClick: false, // Evita que se cierre al hacer clic fuera de la alerta
            }).then(() => {
              // Llamar a la función cargar_gestiones después de cerrar la alerta
              cargar_gestiones();
            });
          },
          error: function () {
            Swal.fire("Error", "Ocurrió un error al agregar el folio", "error");
          },
        });
      }
    });
  });

  // editar folio
  // Evento al hacer clic en el botón con la clase "editarFolio-btn"
  $(document).on("click", ".editarFolio-btn", function () {
    // Obtener el id_folio y el folio actual desde los atributos data
    var id_folio = $(this).data("id_folio");
    var folio_actual = $(this).data("folio");

    // Mostrar el SweetAlert con el formulario de edición
    Swal.fire({
      title: "Editar Folio",
      html: `<form id="editarFolioForm">
              <input type="hidden" id="id_folio" value="${id_folio}">
              <div class="form-group">
                  <label for="folio_actual">Folio Actual</label>
                  <input type="text" id="folio_actual" class="form-control" value="${folio_actual}" disabled>
              </div>
              <div class="form-group">
                  <label for="folio_nuevo">Nuevo Folio</label>
                  <input type="text" id="folio_nuevo" class="form-control" placeholder="Escribe el nuevo folio" required>
              </div>
           </form>`,
      showCancelButton: true,
      confirmButtonText: "Editar",
      cancelButtonText: "Cancelar",
      allowEscapeKey: false, // Evita que se cierre con la tecla Esc
      allowOutsideClick: false, // Evita que se cierre al hacer clic fuera de la alerta
      preConfirm: () => {
        // Validar que el campo folio_nuevo no esté vacío
        const folio_nuevo = Swal.getPopup().querySelector("#folio_nuevo").value;
        if (!folio_nuevo) {
          Swal.showValidationMessage("El nuevo folio es obligatorio");
        }
        return { folio_nuevo: folio_nuevo };
      },
    }).then((result) => {
      if (result.isConfirmed) {
        var folio_nuevo = result.value.folio_nuevo;

        // Enviar los datos mediante AJAX
        $.ajax({
          url: "editarFolio.php", // Cambia 'editar_folio.php' por la ruta de tu archivo PHP
          type: "GET",
          data: {
            id_folio: id_folio,
            folio_nuevo: folio_nuevo,
          },
          success: function (response) {
            // Mostrar SweetAlert de éxito
            Swal.fire({
              title: "¡Éxito!",
              text: "Folio editado correctamente",
              icon: "success",
              allowEscapeKey: false, // Evita que se cierre con la tecla Esc
              allowOutsideClick: false, // Evita que se cierre al hacer clic fuera de la alerta
            }).then(() => {
              // Aquí puedes recargar las gestiones o actualizar la tabla si es necesario
              cargar_gestiones(); // Llamada a la función para cargar las gestiones
            });
          },
          error: function () {
            Swal.fire("Error", "Ocurrió un error al editar el folio", "error");
          },
        });
      }
    });
  });

  $(document).on("click", ".comentarios-btn", function () {
    var id_gestion = $(this).data("id_gestion");

    $.ajax({
      url: "obtener_comentarios.php",
      method: "POST",
      data: { id_gestion: id_gestion },
      success: function (response) {
        var comentarios = JSON.parse(response);
        var html = '<div style="max-height:300px;overflow-y:auto;">';

        comentarios.forEach(function (comentario) {
          if (comentario.tipo === "plaza") {
            html += `
              <div class="comentario-municipio">
                <div class="comentario-bubble">
                  <p><strong>U.N.Tecate:</strong> ${comentario.comentario}</p>
                  <small class="comentario-fecha">${comentario.fecha}</small>
                </div>
              </div>
            `;
          } else {
            html += `
              <div class="comentario-gestor">
                <div class="comentario-bubble">
                  <p><strong>Cliente:</strong> ${comentario.comentario}</p>
                  <small class="comentario-fecha">${comentario.fecha}</small>
                </div>
              </div>
            `;
          }
        });

        html += "</div>";
        html += `
          <textarea id="nuevo-comentario" placeholder="Escribe un nuevo comentario..." class="swal2-textarea"></textarea>
        `;

        Swal.fire({
          title: "Comentarios",
          html: html,
          width: "600px",
          showCloseButton: true,
          confirmButtonText: "Agregar Comentario",
          preConfirm: () => {
            const nuevoComentario =
              document.getElementById("nuevo-comentario").value;
            if (!nuevoComentario) {
              Swal.showValidationMessage("Debes escribir un comentario.");
              return false;
            }
            return nuevoComentario;
          },
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "agregar_comentario.php",
              method: "POST",
              data: {
                id_gestion: id_gestion,
                comentario: result.value,
              },
              success: function () {
                cargar_gestiones(); // Recarga los datos
                Swal.fire("¡Comentario agregado!", "", "success");
              },
              error: function () {
                Swal.fire(
                  "Error",
                  "No se pudo agregar el comentario.",
                  "error"
                );
              },
            });
          }
        });
      },
      error: function () {
        Swal.fire("Error", "No se pudieron obtener los comentarios.", "error");
      },
    });
  });

  // FOTOS

  $("#tblreporte").on("click", ".fotos-btn", function () {
    var fecha = $(this).data("fecha");
    var cuenta = $(this).data("cuenta");

    cargarFotos(fecha, cuenta);
  });

  function cargarFotos(fecha, cuenta) {
    Swal.fire({
      allowEscapeKey: false,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    $.ajax({
      url: "obtener_fotos.php", // Cambia esta URL al archivo PHP que deseas llamar
      method: "GET",
      data: {
        fecha: fecha,
        cuenta: cuenta,
      },
      success: function (response) {
        // Actualizar el contenido del div con el HTML recibido
        $("#fotos").html(response);
        Swal.close();
      },
      error: function (xhr, status, error) {
        Swal.close();
        console.error("Error al cargar las fotos:", error);
        $("#fotos").html(
          "<p>Error al cargar las fotos. Inténtalo de nuevo.</p>"
        );
        Swal.close();
      },
    });
  }

  $("#reporte").click(function (e) {
    e.preventDefault();
    var rangoFechas = $("#rangof").val();
    var cuenta = $("#cuenta").val();

    // Verificar si ambos campos están vacíos
    if (!rangoFechas && !cuenta) {
      toastr.error("Seleccione un rango de fechas o una cuenta a buscar", "", {
        timeOut: 2000,
        positionClass: "toast-top-right",
        closeButton: true,
        progressBar: true,
      });
      return;
    }
    Swal.fire({
      title: "Exportando archivo Excel",
      text: "Espere un momento...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
        $.ajax({
          url: "reporte.php",
          method: "GET",
          data: {
            rango_fechas: rangoFechas,
            cuenta: cuenta,
          },
          xhrFields: {
            responseType: "blob",
          },
          success: function (response) {
            Swal.close();
            const url = window.URL.createObjectURL(new Blob([response]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", "Reporte Gestiones.xlsx");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            Swal.fire({
              title: "Archivo descargado",
              text: "Consulte sus descargas.",
              icon: "success",
            });
          },
          error: function () {
            Swal.close();
            Swal.fire({
              title: "Error",
              text: "No se pudo exportar el archivo.",
              icon: "error",
            });
          },
        });
      },
    });
  });
});
