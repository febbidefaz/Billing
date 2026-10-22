@extends('layouts.app')

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('title', 'Rawat Jalan')

{{-- SAMA DENGAN HALAMAN RAWAT INAP --}}
@section('content_header')
    <div class="d-flex align-items-center">
        <h1 class="mb-0 mr-2">Data Rawat Jalan</h1>
        <span class="badge badge-info shadow-sm px-3 py-2" id="totalPasienHeader"
            style="font-size:14px;font-weight:1000;border-radius:10px;letter-spacing:0.5px;">
            0 Pasien
        </span>
    </div>
@stop

@section('page-content')
    <div class="card shadow-sm">
        {{-- FILTER --}}
        <div class="card-header bg-light">
            <div class="row align-items-end">
                {{-- TANGGAL AWAL --}}
                <div class="col-lg-2 col-md-4 mb-2">
                    <label>Tanggal Awal</label>
                    <input type="text" id="tgl1" class="form-control datepicker" value="{{ date('d-m-Y') }}"
                        autocomplete="off" readonly>
                </div>

                {{-- TANGGAL AKHIR --}}
                <div class="col-lg-2 col-md-4 mb-2">
                    <label>Tanggal Akhir</label>
                    <input type="text" id="tgl2" class="form-control datepicker" value="{{ date('d-m-Y') }}"
                        autocomplete="off" readonly>
                </div>
                <div class="col-lg-3 col-md-4 mb-2">
                    <label for="dokter_id">Dokter</label>
                    <select id="dokter_id" class="form-control">
                        <option value="">Semua Dokter</option>
                        @foreach ($dokter ?? [] as $d)
                            <option value="{{ $d->ID }}">{{ $d->DokterAlias }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 mb-2">
                    <label for="spesialis_id">Spesialis</label>
                    <select id="spesialis_id" class="form-control">
                        <option value="">Semua Spesialis</option>
                        @foreach ($spesialis ?? [] as $s)
                            <option value="{{ $s->ID }}">{{ $s->Spesialis }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 mb-2">
                    <label for="jam_praktek">Jam Praktek</label>
                    <select id="jam_praktek" class="form-control">
                        <option value="">Semua Jam</option>
                        @foreach ($prakOn ?? [] as $p)
                            <option value="{{ $p->ID }}">{{ $p->Alias }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-1 col-md-4 mb-2">
                    <button type="button" id="btnFilter" class="btn btn-info btn-block" title="Tampilkan">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- TABEL: FORMAT RAWAT INAP, KOLOM BILLING TETAP --}}
        <div class="card-body p-0">
            <div class="table-wrap">
                <table id="tblRawatJalan" class="table table-hover table-striped table-bordered nowrap">
                    <thead class="bg-info">
                        <tr>
                            <th>PxRS</th>
                            <th>ID</th>
                            <th>NoRM</th>
                            <th>Nama Pasien</th>
                            <th>Tanggal</th>
                            <th>Alamat</th>
                            <th>Bagian</th>
                            <th>Shift</th>
                            <th>NoSEP</th>
                            <th>No JKN</th>
                            <th>No WA</th>
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
        /* TABEL RAWAT JALAN */
        .table-wrap {
            width: 100%;
            overflow-x: auto;
            overflow-y: auto;
            max-height: 70vh;
            position: relative;
        }

        #tblRawatJalan {
            min-width: 1200px !important;
            width: max-content !important;
        }

        #tblRawatJalan th,
        #tblRawatJalan td {
            white-space: nowrap;
            vertical-align: middle;
        }

        #tblRawatJalan thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #17a2b8 !important;
            color: white !important;
        }

        .table-wrap::-webkit-scrollbar {
            height: 12px;
        }

        .table-wrap::-webkit-scrollbar-thumb {
            background: #999;
            border-radius: 10px;
        }

        /* FILTER */
        .card-header label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .card-header .form-control {
            height: 38px;
        }

        #btnFilter {
            height: 38px;
        }

        /* BOOTSTRAP DATEPICKER */
        .datepicker-dropdown {
            z-index: 1060 !important;
        }

        .datepicker table tr td.active,
        .datepicker table tr td.active:hover {
            background: #17a2b8 !important;
            color: white !important;
        }

        #tgl1,
        #tgl2 {
            background-color: #fff !important;
            cursor: pointer;
        }

        /* Turunkan posisi kalender */
        .datepicker-dropdown {
            margin-top: 60px !important;
            z-index: 1060 !important;
        }
    </style>
@stop



@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.id.min.js">
    </script>

    <script>
        let tableRawatJalan;

        const userId = "{{ auth()->id() ?? 'guest' }}";
        const keyPrefix = "billing_rawat_jalan_" + userId + "_";

        // SIMPAN FILTER
        function simpanFilterRawatJalan() {
            localStorage.setItem(keyPrefix + "tgl_awal", $('#tgl1').val());
            localStorage.setItem(keyPrefix + "tgl_akhir", $('#tgl2').val());
            localStorage.setItem(keyPrefix + "dokter_id", $('#dokter_id').val());
            localStorage.setItem(keyPrefix + "spesialis_id", $('#spesialis_id').val());
            localStorage.setItem(keyPrefix + "jam_praktek", $('#jam_praktek').val());
        }

        // LOAD FILTER
        function loadFilterRawatJalan() {
            const awal = localStorage.getItem(keyPrefix + "tgl_awal");
            const akhir = localStorage.getItem(keyPrefix + "tgl_akhir");
            const dokter = localStorage.getItem(keyPrefix + "dokter_id");
            const spesialis = localStorage.getItem(keyPrefix + "spesialis_id");
            const jam = localStorage.getItem(keyPrefix + "jam_praktek");

            if (awal) $('#tgl1').val(awal);
            if (akhir) $('#tgl2').val(akhir);
            if (dokter !== null) $('#dokter_id').val(dokter);
            if (spesialis !== null) $('#spesialis_id').val(spesialis);
            if (jam !== null) $('#jam_praktek').val(jam);
        }

        // DD-MM-YYYY KE YYYY-MM-DD
        function toDbDate(tgl) {
            if (!tgl) return '';

            const p = tgl.split('-');

            if (p.length !== 3) return tgl;

            return p[2] + '-' + p[1] + '-' + p[0];
        }

        // FORMAT TANGGAL TABEL
        function formatTanggal(data, type) {
            if (!data) return '-';

            if (type === 'sort' || type === 'type') {
                return data;
            }

            const tanggal = String(data).substring(0, 10);
            const p = tanggal.split('-');

            if (p.length !== 3) return data;

            return p[2] + '/' + p[1] + '/' + p[0];
        }

        $(function() {

            // LOAD FILTER TERSIMPAN
            loadFilterRawatJalan();

            // DATEPICKER
            if ($.fn.datepicker) {
                $('#tgl1, #tgl2').datepicker({
                    format: 'dd-mm-yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    language: 'id',
                    orientation: 'bottom auto'
                });

                $('#tgl1, #tgl2').on('click', function() {
                    $(this).datepicker('show');
                });
            } else {
                console.error('Bootstrap Datepicker belum termuat');
            }

            // DATATABLE RAWAT JALAN
            tableRawatJalan = $('#tblRawatJalan').DataTable({

                processing: true,
                stateSave: true,
                responsive: false,
                autoWidth: false,
                scrollX: false,

                stateSaveCallback: function(settings, data) {
                    localStorage.setItem(
                        keyPrefix + "datatable_state",
                        JSON.stringify(data)
                    );
                },

                stateLoadCallback: function(settings) {
                    const state = localStorage.getItem(
                        keyPrefix + "datatable_state"
                    );

                    try {
                        return state ? JSON.parse(state) : null;
                    } catch (e) {
                        return null;
                    }
                },

                ajax: {
                    url: "{{ route('rawatjalan.data') }}",
                    type: "GET",
                    dataSrc: "data",

                    data: function(d) {
                        d.tgl_awal = toDbDate($('#tgl1').val());
                        d.tgl_akhir = toDbDate($('#tgl2').val());
                        d.dokter_id = $('#dokter_id').val();
                        d.spesialis_id = $('#spesialis_id').val();
                        d.jam_praktek = $('#jam_praktek').val();
                    },

                    error: function(xhr) {
                        console.error(
                            'ERROR AJAX RAWAT JALAN:',
                            xhr.status,
                            xhr.responseText
                        );

                        $('#totalPasienHeader').text('Error');

                        if (typeof toastr !== 'undefined') {
                            toastr.error(
                                'Gagal memuat pasien. Periksa Console / Network.'
                            );
                        }
                    }
                },

                columns: [{
                        data: 'PxRS',
                        defaultContent: '-'
                    },
                    {
                        data: 'ID',
                        defaultContent: '-'
                    },
                    {
                        data: 'RegNum',
                        defaultContent: '-'
                    },
                    {
                        data: 'Nama',
                        defaultContent: '-'
                    },
                    {
                        data: 'Tanggal',
                        render: function(data, type) {
                            return formatTanggal(data, type);
                        }
                    },
                    {
                        data: 'Addr',
                        defaultContent: '-'
                    },
                    {
                        data: 'SubLayanan',
                        defaultContent: '-'
                    },
                    {
                        data: 'Alias',
                        defaultContent: '-'
                    },
                    {
                        data: 'NoSEP',
                        defaultContent: '-'
                    },
                    {
                        data: 'NoJKN',
                        defaultContent: '-'
                    },
                    {
                        data: 'NoWA',
                        defaultContent: '-'
                    }
                ],

                createdRow: function(row, data) {
                    $(row).css('cursor', 'pointer');

                    $(row).off('click').on('click', function() {
                        simpanFilterRawatJalan();

                        window.location.href =
                            "{{ route('rawatjalan.detail', ':id') }}"
                            .replace(':id', data.ID);
                    });
                },

                paging: true,
                pageLength: 200,

                lengthMenu: [
                    [50, 100, 150, 200, -1],
                    [50, 100, 150, 200, "Semua"]
                ],

                ordering: true,
                order: [
                    [1, 'desc']
                ],

                drawCallback: function(settings) {
                    const total = settings.fnRecordsDisplay();

                    $('#totalPasienHeader').text(total + ' Pasien');
                },

                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    zeroRecords: "Data pasien tidak ditemukan",
                    processing: "Memuat data...",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });

            // SIMPAN FILTER
            $('#tgl1, #tgl2, #dokter_id, #spesialis_id, #jam_praktek')
                .on('change', function() {
                    simpanFilterRawatJalan();
                });

            // TOMBOL TAMPILKAN
            $('#btnFilter').off('click').on('click', function() {

                simpanFilterRawatJalan();

                console.log('Filter Billing:', {
                    tanggal_awal: $('#tgl1').val(),
                    tanggal_akhir: $('#tgl2').val(),
                    dokter_id: $('#dokter_id').val(),
                    spesialis_id: $('#spesialis_id').val(),
                    jam_praktek: $('#jam_praktek').val()
                });

                tableRawatJalan.ajax.reload(null, true);
            });

        });
    </script>
@endpush
