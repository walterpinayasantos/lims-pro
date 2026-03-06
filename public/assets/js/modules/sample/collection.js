/**
 * LIMS ARCHITECT PRO - Módulo de Flebotomía
 * Ubicación: public/assets/js/modules/sample/collection.js
 */
(function ($) {
    "use strict";

    // Variables Globales del Módulo
    const elements = {
        workArea: $("#work_area"),
        emptyArea: $("#empty_work_area"),
        patientName: $("#selected_patient_name"),
        orderCode: $("#selected_order_code"),
        tableBody: $("#samples_list_body"),
        btnConfirm: "#btn_confirm_collection", // Selector string para delegación
    };

    // Inicialización
    $(document).ready(function () {
        console.log("✅ Módulo de Flebotomía Cargado Correctamente");
    });

    // -------------------------------------------------------------------------
    // 1. FUNCIÓN GLOBAL (Expuesta a window para llamarla desde el HTML onclick)
    // -------------------------------------------------------------------------
    window.loadPatientSamples = function (orderId, patientName, orderCode) {
        console.log("-> Cargando paciente:", patientName);

        // UI Reset
        elements.emptyArea.hide();
        elements.workArea.fadeIn();
        elements.patientName.text(patientName);
        elements.orderCode.text("Orden #" + orderCode);
        elements.tableBody.html(
            '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div> Consultando Metadata...</td></tr>',
        );

        // AJAX Request
        $.get(window.BASE_URL + "sample/getPendingItems/" + orderId)
            .done(function (res) {
                if (res.success) {
                    renderTable(res.items);
                } else {
                    alert("Error: " + res.message);
                }
            })
            .fail(function (xhr) {
                console.error(xhr);
                elements.tableBody.html(
                    '<tr><td colspan="5" class="text-center text-danger">Error de conexión al cargar muestras.</td></tr>',
                );
            });
    };

    // -------------------------------------------------------------------------
    // 2. RENDERIZADO DE LA TABLA (Lógica Visual)
    // -------------------------------------------------------------------------
    function renderTable(items) {
        let html = "";

        if (items.length === 0) {
            html =
                '<tr><td colspan="5" class="text-center text-success fw-bold py-3"><i class="ti ti-check-double fs-1"></i><br>Todas las muestras fueron recolectadas.</td></tr>';
            // Ocultamos el botón usando CSS directo o jQuery
            $(elements.btnConfirm).hide();
        } else {
            // Mostramos el botón
            $(elements.btnConfirm).show();

            items.forEach((item) => {
                // Lógica de Colores de Tubos (Metáfora visual)
                let badgeClass = "bg-secondary";
                let cType = (item.container_type || "").toLowerCase();

                if (cType.includes("lila") || cType.includes("edta"))
                    badgeClass = "bg-primary"; // Azul/Lila
                else if (cType.includes("rojo") || cType.includes("seco"))
                    badgeClass = "bg-danger"; // Rojo
                else if (cType.includes("amarillo") || cType.includes("gel"))
                    badgeClass = "bg-warning text-dark"; // Amarillo
                else if (cType.includes("celeste") || cType.includes("citrato"))
                    badgeClass = "bg-info"; // Celeste
                else if (cType.includes("orina") || cType.includes("frasco")) badgeClass = "bg-success"; // Verde

                html += `
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input sample-checkbox form-check-input-lg" value="${item.id}" checked>
                        </td>
                        <td>
                            <span class="badge ${badgeClass} p-2 fs-6 w-100 shadow-sm text-start">
                                <i class="ti ti-test-pipe me-1"></i> ${item.container_type}
                            </span>
                        </td>
                        <td class="fw-bold text-dark">${item.sample_volume || "-"}</td>
                        <td>
                            <div class="fw-bold text-dark">${item.name}</div>
                            <small class="text-muted font-monospace">${item.sample_type}</small>
                        </td>
                        <td>
                            ${item.patient_preparation ? `<span class="badge bg-light text-dark border"><i class="ti ti-alert-circle me-1"></i> ${item.patient_preparation}</span>` : "-"}
                        </td>
                    </tr>
                `;
            });
        }
        elements.tableBody.html(html);
    }

    // -------------------------------------------------------------------------
    // 3. EVENTOS (Delegación Estricta)
    // -------------------------------------------------------------------------

    // Check All
    $(document).on("change", "#check_all", function () {
        $(".sample-checkbox").prop("checked", $(this).is(":checked"));
    });

    // BOTÓN DE CONFIRMACIÓN (Aquí estaba el error silencioso)
    // Usamos $(document).on para capturar el click aunque el botón se haya re-creado
    $(document).on("click", elements.btnConfirm, function (e) {
        e.preventDefault(); // Prevenir cualquier submit nativo

        console.log("Botón presionado..."); // Debug

        let selectedIds = [];
        $(".sample-checkbox:checked").each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert("Por favor, seleccione al menos una muestra para recolectar.");
            return;
        }

        if (!confirm("¿Confirma que ha recolectado y etiquetado " + selectedIds.length + " tubos?")) return;

        // Efecto Loading
        let $btn = $(this);
        let originalText = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Guardando...');

        // AJAX POST
        $.ajax({
            url: window.BASE_URL + "sample/collect",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ item_ids: selectedIds }),
            success: function (res) {
                if (res.success) {
                    alert("✅ Muestras Confirmadas Exitosamente.");
                    window.location.reload();
                } else {
                    alert("Error: " + res.message);
                    $btn.prop("disabled", false).html(originalText);
                }
            },
            error: function (xhr) {
                console.error("Error Servidor:", xhr.responseText);
                alert("Error técnico al guardar. Ver consola.");
                $btn.prop("disabled", false).html(originalText);
            },
        });
    });
})(jQuery);
