/**
 * LIMS ARCHITECT PRO - Worksheet (Hoja de Trabajo Electrónica)
 * Maneja la validación de rangos, pánicos y persistencia vía AJAX.
 * * Estándar: PSR-4 Compatible & Modular JS
 */
(function ($) {
    "use strict";

    $(document).ready(function () {
        console.log("✅ LIMS: Módulo de Resultados activado.");

        // Disparar validación inicial por si hay datos precargados
        $(".result-input").trigger("input");
    });

    // -------------------------------------------------------------------------
    // 1. VALIDACIÓN DINÁMICA (SEMÁFORO MÉDICO)
    // -------------------------------------------------------------------------
    $(document).on("input", ".result-input", function () {
        let input = $(this);
        let row = input.closest("tr");
        let badge = row.find(".flag-badge");
        let val = parseFloat(input.val());

        // Recuperar metadatos técnicos de los data-attributes del TR
        let low = parseFloat(row.data("low"));
        let high = parseFloat(row.data("high"));
        let critLow = parseFloat(row.data("crit-low"));
        let critHigh = parseFloat(row.data("crit-high"));

        // Reset visual preventivo
        badge.hide().removeClass("bg-warning bg-danger text-white text-dark").text("");
        input.removeClass("border-warning border-danger text-danger fw-bold");

        if (isNaN(val)) return;

        // Lógica de Pánico (Prioridad Máxima)
        if ((!isNaN(critLow) && val < critLow) || (!isNaN(critHigh) && val > critHigh)) {
            badge.show().addClass("bg-danger text-white").text("**");
            input.addClass("border-danger text-danger fw-bold");
            return;
        }

        // Lógica de Rango de Referencia (H/L)
        if (!isNaN(low) && val < low) {
            badge.show().addClass("bg-warning text-dark").text("L");
            input.addClass("border-warning");
        } else if (!isNaN(high) && val > high) {
            badge.show().addClass("bg-warning text-dark").text("H");
            input.addClass("border-warning");
        }
    });

    // -------------------------------------------------------------------------
    // 2. PERSISTENCIA DE DATOS (AJAX)
    // -------------------------------------------------------------------------
    $("#btn_save_results").on("click", function (e) {
        e.preventDefault();

        let btn = $(this);
        let orderId = $("#worksheet_container").data("order-id");
        let results = [];

        // Auditoría de filas: Capturamos solo lo que tiene valor
        $(".result-row").each(function () {
            let row = $(this);
            let val = row.find(".result-input").val().trim();

            if (val !== "") {
                results.push({
                    order_item_id: row.data("item-id"),
                    result_value: val,
                    comments: row.find(".result-comment").val(),
                });
            }
        });

        // Validación de pre-envío
        if (results.length === 0) {
            Swal.fire({
                icon: "warning",
                title: "Atención",
                text: "Debe ingresar al menos un resultado para guardar.",
                confirmButtonColor: "#3085d6",
            });
            return;
        }

        // Bloqueo de UI (Evita duplicados)
        let originalContent = btn.html();
        btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-1"></span> Procesando...');

        $.ajax({
            url: window.BASE_URL + "result/store",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                order_id: orderId,
                results: results,
            }),
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: "success",
                        title: "¡Éxito!",
                        text: "Resultados validados correctamente.",
                        timer: 2000,
                        showConfirmButton: false,
                    }).then(() => {
                        window.location.href = window.BASE_URL + "result";
                    });
                } else {
                    Swal.fire("Error", res.message || "Error desconocido", "error");
                    btn.prop("disabled", false).html(originalContent);
                }
            },
            error: function (xhr) {
                console.error("LIMS Error:", xhr.responseText);
                Swal.fire("Error Crítico", "No se pudo conectar con el servidor. Revise la consola.", "error");
                btn.prop("disabled", false).html(originalContent);
            },
        });
    });
})(jQuery);
