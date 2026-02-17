<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-transparent">
            <form action="<?= BASE_URL ?>search" method="GET">
                <div class="card mb-1">
                    <div class="px-3 py-2 d-flex flex-row align-items-center" id="top-search">
                        <i class="ti ti-search fs-22"></i>
                        <input
                            type="search"
                            class="form-control border-0"
                            id="search-modal-input"
                            name="q"
                            placeholder="Buscar pacientes, doctores, órdenes..." />
                        <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close">
                            <span class="badge bg-light text-dark border">ESC</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>