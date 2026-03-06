/**
 * LIMS ARCHITECT PRO - Módulo de Revisión y Firma Facultativa
 * Permite la validación final y corrección antes de la emisión del informe.
 */
(function ($) {
    "use strict";

    $(document).ready(function () {
        console.log("✅ LIMS: Módulo de Firma Facultativa cargado.");
    });

    /**
     * Acción de Aprobar y Firmar
     */
    $("#btn_approve_signature").on("click", function (e) {
        e.preventDefault();

        let btn = $(this);
        let orderId = $("#review_container").data("order-id"); // Contenedor principal de la vista
        let results = [];

        // Recolectamos los datos de la tabla de revisión
        $(".review-row").each(function () {
            let row = $(this);
            results.push({
                order_item_id: row.data("item-id"),
                result_value: row.find(".result-input").val(), // Captura si hubo corrección
                comments: row.find(".result-comment").val(),
            });
        });

        // Confirmación Profesional
        Swal.fire({
            title: "¿Confirmar Firma Facultativa?",
            text: "Una vez firmado, el informe será oficial y no podrá editarse.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            confirmButtonText: '<i class="ti ti-edit"></i> Sí, Firmar y Validar',
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                // Bloqueo de UI para evitar doble firma
                let originalHtml = btn.html();
                btn.prop("disabled", true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Firmando...',
                );

                $.ajax({
                    url: window.BASE_URL + "result/approve",
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
                                title: "¡Informe Validado!",
                                text: "La orden ha pasado al estado de Entrega de Resultados.",
                                timer: 2000,
                                showConfirmButton: false,
                            }).then(() => {
                                window.location.href = window.BASE_URL + "result/signature"; // Regresa a la bandeja de firmas
                            });
                        } else {
                            Swal.fire("Error", res.message, "error");
                            btn.prop("disabled", false).html(originalHtml);
                        }
                    },
                    error: function (xhr) {
                        console.error("LIMS Error:", xhr.responseText);
                        Swal.fire("Error Crítico", "No se pudo completar la firma. Revise la conexión.", "error");
                        btn.prop("disabled", false).html(originalHtml);
                    },
                });
            }
        });
    });
})(jQuery);
