/**
 * Módulo: Patients Index
 * Descripción: Manejo de DataTables, Eliminado Lógico y Restauración (Papelera)
 */
$(document).ready(function () {
    "use strict";

    const tableId = "#pat_table_list";
    let isTrashMode = false; // Estado inicial: Mostrando activos

    if ($(tableId).length) {
        // 1. Inicialización del DataTable
        const table = $(tableId).DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
            },
            ajax: {
                url: window.BASE_URL + "patient/get_datatable",
                type: "GET",
                data: function (d) {
                    // Enviamos el parámetro 'trash' al controlador
                    // true = traer eliminados, false = traer activos
                    d.trash = isTrashMode;
                },
                dataSrc: "data",
            },
            columns: [
                {
                    data: "document_id",
                    render: function (data, type, row) {
                        return `<span class="fw-bold text-dark">${data}</span>`;
                    },
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<div class="d-flex flex-column">
                                    <span class="fw-semibold">${row.last_name}, ${row.first_name}</span>
                                    <small class="text-muted">${row.email || "Sin email"}</small>
                                </div>`;
                    },
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        let badgeClass =
                            row.gender === "F"
                                ? "bg-danger-subtle text-danger"
                                : row.gender === "M"
                                  ? "bg-info-subtle text-info"
                                  : "bg-warning-subtle text-warning";

                        let extraInfo = "";

                        // VERIFICACIÓN ROBUSTA
                        if (isTrashMode) {
                            // Si row.deleted_at existe, lo usamos. Si no, ponemos "Desconocido"
                            let fechaBorrado = row.deleted_at ? row.deleted_at : "Desconocido";

                            // Opcional: Cortar la fecha para que no muestre segundos (YYYY-MM-DD HH:MM)
                            if (fechaBorrado.length > 16) fechaBorrado = fechaBorrado.substring(0, 16);

                            extraInfo = `<div class='mt-1 text-danger small'><i class='ti ti-trash'></i> ${fechaBorrado}</div>`;
                        } else {
                            extraInfo = `<span class="ms-1">${row.age} Años</span>`;
                        }

                        return `<span class="badge ${badgeClass}">${row.gender}</span> ${extraInfo}`;
                    },
                },
                { data: "phone" },
                {
                    data: "id",
                    orderable: false,
                    render: function (data, type, row) {
                        // 🧠 MAGIA: Renderizado Condicional según el Modo
                        if (isTrashMode) {
                            // MODO PAPELERA: Botón Restaurar
                            return `
                                <button class="btn btn-sm btn-success btn-restore-patient w-100" data-id="${data}">
                                    <i class="ti ti-rotate-clockwise"></i> Restaurar
                                </button>
                            `;
                        } else {
                            // MODO ACTIVOS: Menú de Acciones Normal
                            return `
                                <div class="btn-group dropdown">
                                    <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-light btn-xs" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="${window.BASE_URL}patient/edit/${data}"><i class="ti ti-edit me-2 text-success"></i>Editar</a>
                                        <a class="dropdown-item" href="${window.BASE_URL}patient/history/${data}"><i class="ti ti-activity me-2 text-primary"></i>Historial</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item btn-delete-patient" href="javascript:void(0);" data-id="${data}"><i class="ti ti-trash me-2 text-danger"></i>Eliminar</a>
                                    </div>
                                </div>`;
                        }
                    },
                },
            ],
            order: [[1, "asc"]],
            drawCallback: function () {
                $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
            },
        });

        // 2. EVENTO: Cambio de Modo (Activos vs Papelera)
        $('input[name="filter_status"]').on("change", function () {
            // Actualizamos la variable de estado
            isTrashMode = $(this).val() === "trash";

            // UI UX: Ocultar botón "Nuevo Paciente" si estamos en la basura
            if (isTrashMode) {
                $("#pat_btn_new").fadeOut();
                // CAMBIO DINÁMICO DEL TÍTULO
                $("#th_age_info").html("Sexo / Eliminación");
            } else {
                $("#pat_btn_new").fadeIn();
                // RESTAURAR TÍTULO ORIGINAL
                $("#th_age_info").html("Edad / Sexo");
            }

            // Recargamos la tabla (Esto dispara de nuevo 'ajax' con el nuevo valor de d.trash)
            table.ajax.reload();
        });

        // 3. EVENTO: Restaurar Paciente
        $(document).on("click", ".btn-restore-patient", function () {
            let id = $(this).data("id");
            if (confirm("¿Desea restaurar este paciente a la lista activa?")) {
                $.ajax({
                    url: window.BASE_URL + "patient/restore/" + id,
                    type: "POST",
                    dataType: "json",
                    success: function (res) {
                        if (res.success) {
                            table.ajax.reload(); // Refrescamos para ver que se fue de la papelera
                            // Opcional: Toast de éxito
                            alert("✅ Paciente restaurado correctamente.");
                        } else {
                            alert("Error: " + res.message);
                        }
                    },
                    error: function () {
                        alert("Error de conexión al restaurar.");
                    },
                });
            }
        });

        // 4. EVENTO: Eliminar Paciente (Mover a Papelera)
        $(document).on("click", ".btn-delete-patient", function () {
            let id = $(this).data("id");

            if (confirm("¿Está seguro de eliminar este paciente? Podrá recuperarlo desde la Papelera.")) {
                $.ajax({
                    url: window.BASE_URL + "patient/delete/" + id,
                    type: "POST",
                    dataType: "json",
                    success: function (res) {
                        if (res.success) {
                            table.ajax.reload();
                            alert("🗑️ Paciente movido a la papelera.");
                        } else {
                            alert("Error: " + res.message);
                        }
                    },
                    error: function () {
                        alert("Error de conexión al eliminar.");
                    },
                });
            }
        });
    }
});
