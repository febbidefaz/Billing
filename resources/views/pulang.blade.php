@extends('layouts.app')
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('title', 'Pasien Pulang')

@section('content_header')
    <div class="d-flex align-items-center">
        <h1 class="mb-0 mr-2">Data Pasien Pulang</h1>

        <span class="badge badge-info shadow-sm px-3 py-2" id="totalPasienHeader"
            style="font-size:14px;font-weight:1000;border-radius:10px;letter-spacing:0.5px;">
            0 Pasien
        </span>
    </div>
@stop

@section('page-content')

    <div class="card shadow-sm">

        <div class="card-header bg-light">
            <div class="row align-items-center">

                <div class="col-md-3">
                    <div class="form-group row mb-0">
                        <label class="col-sm-4 col-form-label">TGL Awal</label>
                        <div class="col-sm-8">
                            <input type="text" id="tglAwal" class="form-control datepicker" value="{{ date('d-m-Y') }}"
                                autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group row mb-0">
                        <label class="col-sm-4 col-form-label">TGL Akhir</label>
                        <div class="col-sm-8">
                            <input type="text" id="tglAkhir" class="form-control datepicker" value="{{ date('d-m-Y') }}"
                                autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-info btn-block" onclick="reloadPasienPulang()">
                        <i class="fas fa-search"></i>
                        Tampilkan
                    </button>
                </div>

            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-wrap">
                <table id="tblPasienPulang" class="table table-hover table-striped table-bordered nowrap">
                    <thead class="bg-info">
                        <tr>
                            <th>PxRS</th>
                            <th>ID</th>
                            <th>NoRM</th>
                            <th>Nama Pasien</th>
                            <th>Ruang</th>
                            <th>T. Masuk</th>
                            <th>T. Keluar</th>
                            <th>Status</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>

@stop

@section('css')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
    <style>
        .table-wrap {
            width: 100%;
            overflow-x: auto;
            overflow-y: auto;
            max-height: 70vh;
            position: relative;
        }

        #tblPasienPulang {
            min-width: 900px !important;
            width: max-content !important;
        }

        #tblPasienPulang th,
        #tblPasienPulang td {
            white-space: nowrap;
            vertical-align: middle;
        }

        #tblPasienPulang thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #17a2b8 !important;
            color: white;
        }
    </style>
@stop

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.id.min.js">
    </script>
    <script>
        let tablePasienPulang;

        const userId = "{{ auth()->id() }}";
        const keyPrefix = "pasien_pulang_" + userId + "_";

        function toDbDate(tgl) {

            if (!tgl) return '';

            let p = tgl.split('-');

            if (p.length !== 3) return tgl;

            return p[2] + '-' + p[1] + '-' + p[0];
        }

        function simpanFilterPulang() {

            localStorage.setItem(
                keyPrefix + "tgl_awal",
                $('#tglAwal').val()
            );

            localStorage.setItem(
                keyPrefix + "tgl_akhir",
                $('#tglAkhir').val()
            );
        }

        function loadFilterPulang() {

            let savedAwal = localStorage.getItem(
                keyPrefix + "tgl_awal"
            );

            let savedAkhir = localStorage.getItem(
                keyPrefix + "tgl_akhir"
            );

            if (savedAwal) {
                $('#tglAwal').val(savedAwal);
            }

            if (savedAkhir) {
                $('#tglAkhir').val(savedAkhir);
            }
        }

        $(function() {
            $('.datepicker').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true,
                language: 'id'
            });
            loadFilterPulang();
            tablePasienPulang = $("#tblPasienPulang").DataTable({
                processing: true,
                stateSave: true,

                ajax: {
                    url: "{{ route('pulang.data') }}",
                    data: function(d) {
                        d.tgl_awal = toDbDate($('#tglAwal').val());
                        d.tgl_akhir = toDbDate($('#tglAkhir').val());
                    }
                },

                columns: [{
                        data: 'PxRS'
                    },
                    {
                        data: 'ID'
                    },
                    {
                        data: 'RegNum'
                    },
                    {
                        data: 'Nama'
                    },
                    {
                        data: 'RoomName'
                    },
                    {
                        data: 'TMasuk',
                        render: function(data) {
                            return formatTanggal(data);
                        }
                    },
                    {
                        data: 'LDate',
                        render: function(data) {
                            return formatTanggal(data);
                        }
                    },
                    {
                        data: 'Status'
                    },
                    {
                        data: 'Addr'
                    }
                ],

                createdRow: function(row, data) {
                    $(row).css('cursor', 'pointer');

                    $(row).on('click', function() {

                        simpanFilterPulang();

                        window.location.href =
                            "{{ route('pulang.detail', ':id') }}".replace(':id', data.ID);
                    });
                },

                responsive: false,
                autoWidth: false,
                scrollX: false,

                paging: true,
                pageLength: 200,

                ordering: true,
                order: [
                    [6, 'desc']
                ],

                drawCallback: function(settings) {
                    let total = settings.fnRecordsTotal();
                    $('#totalPasienHeader').html(total + ' Pasien');
                },

                lengthMenu: [
                    [50, 100, 150, 200, -1],
                    [50, 100, 150, 200, "Semua"]
                ],

                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });

            $('#tglAwal,#tglAkhir').on('change', function() {
                simpanFilterPulang();
            });

        });

        function reloadPasienPulang() {
            simpanFilterPulang();
            tablePasienPulang.ajax.reload();
        }

        function formatTanggal(data) {
            if (!data) return '-';

            let tgl = new Date(data);
            let dd = String(tgl.getDate()).padStart(2, '0');
            let mm = String(tgl.getMonth() + 1).padStart(2, '0');
            let yyyy = tgl.getFullYear();

            return dd + '/' + mm + '/' + yyyy;
        }
    </script>
@endpush
