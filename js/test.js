$(document).ready(function () {
  // Inicializar el daterangepicker
  $("#rangof").daterangepicker({
    autoUpdateInput: false,
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

  // Inicializar Select2
  $("#cuenta").select2({
    placeholder: "-- Selecciona una cuenta --",
    allowClear: true,
    width: "100%",
  });
  $("#gestor").select2({
    placeholder: "-- Selecciona un gestor --",
    allowClear: true,
    width: "100%",
  });

  // Inicializar DataTable con 10 registros por defecto y ocultar la opción de seleccionar registros por página
var tabla = $("#tblreporte").DataTable({
  scrollX: true, // Habilitar el scroll horizontal
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
            var tipo;
            if (
                row.registro == "Gestor" ||
                row.registro == "Abogado" ||
                row.registro == "Carta Invitacion" ||
                row.registro == "Reductores"
            ) {
                if (row.registro == "Gestor") {
                    tipo = 5;
                } else if (row.registro == "Abogado") {
                    tipo = 2;
                } else if (row.registro == "Carta Invitacion") {
                    tipo = 6;
                } else if (row.registro == "Reductores") {
                    tipo = 7;
                }
        
                var fecha = new Date(row.fecha.date); // Asumiendo que row.fecha es un objeto
                var formattedDate = fecha.toISOString().split("T")[0];
                
                var viewButton = `<a href="fotos.php?fecha=${formattedDate}&tipo=${tipo}&cta=${row.cuenta}" Target="_blank" class="btn btn-outline-primary btn-sm" title="Ver gestion" style="padding:0%;border:0px;">
                    <img src="https://img.icons8.com/color/16/fine-print.png" alt="fine-print"/>
                </a>`;
                var editButton = `<button class="btn btn-outline-secondary btn-sm editar-btn" data-fecha="${formattedDate}" data-tipo="${tipo}" data-cuenta="${row.cuenta}" title="Editar gestión" style="padding:0%;border:0px; font-size: 1em">
                <img src="https://img.icons8.com/external-anggara-flat-anggara-putra/16/external-edit-user-interface-anggara-flat-anggara-putra-5.png" /> Editar
            </button>`;
            } else {
                var viewButton = '';
                var editButton = '';
            }
        
            
        
            return viewButton + editButton;
        }
        
      },
      { data: "cuenta" },
      { data: "registro" },
      { data: "tarea" },
      { data: "propietario" },
      { data: "calle" },
      { data: "numext" },
      { data: "numint" },
      { data: "colonia" },
      { data: "cp" },
      { data: "adeudoa" },
      { data: "adeudoi" },
      { data: "latitud" },
      { data: "longitud" },
      { data: "gestor" },
      { data: "medidor" },
      {
          data: "fecha",
          render: function (data, type, row) {
              // Verificar si 'data' es un objeto
              if (typeof data === "object") {
                  // Obtener la fecha del objeto y convertirla en un objeto Date
                  var fecha = new Date(data.date);

                  // Formatear la fecha como "YYYY-MM-DD"
                  var formattedDate = fecha.toISOString().split("T")[0];

                  // Devolver la fecha formateada
                  return formattedDate;
              } else {
                  // Si 'data' no es un objeto, devolverlo como está
                  return data;
              }
          },
      },
      {
          data: null,
          render: function (data, type, row) {
              // Redondear la latitud hacia abajo
              var latitudRedondeada = Math.floor(row.latitud);

              // Verificar si la latitud redondeada es 0
              if (latitudRedondeada === 0) {
                  return ""; // No mostrar el botón
              }

              var geopunto = row.geopunto;
              return `<a href="${geopunto}" target="_blank" class="btn btn-outline-primary btn-sm" title="Ver gestión" style="padding:0%;border:0px;font-size: 1em">
                  <img src="https://img.icons8.com/color/16/google-maps.png"/>
                  Ver &nbsp;
              </a>`;
          },
      },
  ],
});
var filaActual; // Variable para almacenar la fila actual que se está editando

  // Captura el clic del botón "Buscar"
  $("#buscar").on("click", function () {
    // Obtiene las fechas del daterangepicker
    var rangoFechas = $("#rangof").val();
    var tipo = $("#tipo").val();
    var cuenta = $("#cuenta").val();
    var gestor = $("#gestor").val();

    // Verifica si se seleccionó un rango de fechas
    if (!rangoFechas) {
      toastr.error("Seleccione un rango de fechas", "", {
        timeOut: 2000,
        positionClass: "toast-top-right",
        closeButton: true,
        progressBar: true,
      });
      return; // Detiene la ejecución del código
    }
    $.ajax({
      url: "obtenerGestiones.php",
      type: "GET",
      dataType: "json",
      data: {
        rango_fechas: rangoFechas,
        tipo: tipo,
        cuenta: cuenta,
        gestor: gestor,
      },
      success: function (response) {
        // Actualizar DataTable con nuevos datos
        tabla.clear().rows.add(response.data).draw();

        // Actualizar opciones del select de cuentas
        $("#cuenta")
          .empty()
          .append('<option value="">-- Selecciona una cuenta --</option>');
        response.cuentas.forEach(function (cuenta) {
          $("#cuenta").append(
            '<option value="' + cuenta + '">' + cuenta + "</option>"
          );
        });

        // Actualizar opciones del select de gestores
        $("#gestor")
          .empty()
          .append('<option value="">-- Selecciona un gestor --</option>');
        response.gestores.forEach(function (gestor) {
          $("#gestor").append(
            '<option value="' + gestor + '">' + gestor + "</option>"
          );
        });

        // Ocultar alerta de carga al recibir la respuesta
        Swal.close();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        Swal.close();
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Error al consultar obtener los datos, de preferencia seleccione un rango de fechas mas corto!",
        });
        console.error("Error al obtener las gestiones y cuentas:", errorThrown);
      },
      beforeSend: function () {
        // Mostrar alerta de carga al enviar la petición AJAX
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
  });

  // Función para actualizar las cuentas en el select
  function actualizarCuentas(cuentas) {
    var selectCuenta = $("#cuenta");
    selectCuenta.empty(); // Limpiar opciones existentes
    selectCuenta.append(
      $("<option>", {
        value: "",
        text: "",
      })
    );
    cuentas.forEach(function (cuenta) {
      selectCuenta.append(
        $("<option>", {
          value: cuenta,
          text: cuenta,
        })
      );
    });
  }

  function actualizarGestores(gestores) {
    var selectGestor = $("#gestor");
    selectGestor.empty(); // Limpiar opciones existentes
    selectGestor.append(
      $("<option>", {
        value: "",
        text: "",
      })
    );
    gestores.forEach(function (gestor) {
      selectGestor.append(
        $("<option>", {
          value: gestor,
          text: gestor,
        })
      );
    });
  }

  // Función para actualizar las gestiones en la tabla
  function actualizarGestiones(gestiones) {
    var tabla = $("#tblreporte").DataTable();
    tabla.clear().draw(); // Limpiar tabla antes de actualizar
    tabla.rows.add(gestiones).draw(); // Agregar nuevas filas y dibujar tabla
  }
});

$(document).ready(function () {
  $("#reporte").click(function (e) {
    e.preventDefault();

    Swal.fire({
      title: "Exportando archivo Excel",
      text: "Espere un momento...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
        $.ajax({
          url: "reporte.php",
          method: "GET",
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


$(document).ready(function() {
  $(document).on('click', '.editar-btn', function() {
    var fecha = $(this).data('fecha');
    var tipo = $(this).data('tipo');
    var cuenta = $(this).data('cuenta');

    filaActual = tabla.row($(this).closest('tr')); // Guardar la fila actual

    $.ajax({
      url: 'obtener_datos.php', // Cambia esto por la URL correcta de tu PHP
      method: 'GET',
      data: {
        fecha: fecha,
        tipo: tipo,
        cta: cuenta
      },
      dataType: 'json',
      success: function(response) {
        if (response.error) {
          alert(response.error);
        } else {
          $('#idGestion').val(response.id_gestion);
          $('#fechaGestion').val(response.fecha);
          $('#tabla').val(response.tabla);
          $('#idRegistro').val(response.idRegistro);

          $('#selectGestores').empty();
          response.gestores.forEach(function(gestor) {
            var selected = gestor.Id == response.gestor_actual ? 'selected' : '';
            $('#selectGestores').append(`<option value="${gestor.Id}" ${selected}>${gestor.Usuario}</option>`);
          });

          $('#selectTareas').empty();
          response.tareas.forEach(function(tarea) {
            var selected = tarea.IdTarea == response.tarea_actual ? 'selected' : '';
            $('#selectTareas').append(`<option value="${tarea.IdTarea}" ${selected}>${tarea.DescripcionTarea}</option>`);
          });

          $('#modalGestion').modal('show');
        }
      },
      error: function(xhr, status, error) {
        alert('Error al obtener los datos: ' + error);
      }
    });
  });
});


$(document).ready(function () {
  // Manejar el clic en el botón con el ID "btnActualizarGestion"
  $('#btnActualizarGestion').on('click', function () {
      // Obtener los valores del formulario
      var idGestion = $("#idGestion").val();
      var fechaGestion = $("#fechaGestion").val();
      var selectGestores = $("#selectGestores").val();
      var selectTareas = $("#selectTareas").val();
      var tablaNombre = $("#tabla").val();
      var idRegistro = $("#idRegistro").val();

      // Validar que no estén vacíos
      if (!idGestion || !fechaGestion || !selectGestores || !selectTareas || !tabla || !idRegistro) {
          Swal.fire({
              icon: 'error',
              title: 'Campos vacíos',
              text: 'Por favor, completa todos los campos.',
          });
          return; // Detiene la ejecución del código si hay campos vacíos
      }
// console.log(tabla+' - '+idRegistro+' - '+idGestion+' - '+fechaGestion+' - '+selectGestores+' - '+selectTareas);
// return;
      // Enviar los datos al archivo PHP usando AJAX
      $.ajax({
          url: 'actualizarGestion.php', // Archivo PHP que maneja la actualización
          type: 'GET',
          dataType: 'json',
          data: {
              tabla: tablaNombre, // Asegúrate de enviar el nombre de la tabla
              idRegistro: idRegistro, // Asegúrate de enviar el nombre de la columna de ID
              idGestion: idGestion,
              fecha: fechaGestion,
              gestor: selectGestores,
              tarea: selectTareas
          },
          success: function (response) {
              // Verificar la respuesta del servidor
              if (response.success) {
                var updatedData = filaActual.data();
                updatedData.fecha.date = fechaGestion;
                updatedData.gestor = selectGestores;
                updatedData.tarea = selectTareas;
                filaActual.data(updatedData).draw();
                  // Cerrar el modal
                  $('#modalGestion').modal('hide');

                  // Mostrar mensaje de éxito
                  Swal.fire({
                      icon: 'success',
                      title: 'Actualización exitosa',
                      text: response.message || 'Los datos han sido actualizados correctamente.',
                  });
              } else {
                  // Mostrar mensaje de error
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: response.message || 'Hubo un problema al actualizar los datos.',
                  });
              }
          },
          error: function (jqXHR, textStatus, errorThrown) {
              // Manejar el error
              Swal.fire({
                  icon: 'error',
                  title: 'Error de servidor',
                  text: 'No se pudo conectar al servidor.',
              });
              console.error("Error al actualizar la gestión:", errorThrown);
          }
      });
  });
});


