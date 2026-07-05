@extends('layouts.app')
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('title', 'Monitoring RI')

@section('content_header')
    <div class="d-flex align-items-center">
        <h1 class="mb-0 mr-2">Monitoring Rawat Inap</h1>

        <span class="badge badge-info shadow-sm px-3 py-2" id="totalPasienHeader"
            style="font-size:14px;font-weight:1000;border-radius:10px;letter-spacing:0.5px;">
            0 Pasien
        </span>
    </div>
@stop

@section('page-content')

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-wrap">
                <table id="tblMonitoringRI" class="table table-hover table-striped table-bordered nowrap">
                    <thead class="bg-info">
                        <tr>
                            <th style="width:30px">ID</th>
                            <th style="width:150px">Nama Pasien</th>
                            <th style="width:40px">Pembiayaan</th>
                            <th style="width:40px">Tgl MRS</th>
                            <th style="width:5px">Los</th>
                            <th style="width:5px">H Kls</th>
                            <th style="width:20px">Coding</th>
                            <th style="width:20px">Billing</th>
                            <th style="width:200px">DPJP</th>
                            <th style="width:40px">Ruang</th>
                        </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

@stop

@section('css')
    <style>
        .table-wrap {
            width: 100%;
            overflow-x: auto;
            overflow-y: auto;
            max-height: 70vh;
            position: relative;
        }

        #tblMonitoringRI {
            min-width: 900px !important;
            width: max-content !important;
        }

        #tblMonitoringRI th,
        #tblMonitoringRI td {
            white-space: nowrap;
            vertical-align: middle;
        }

        #tblMonitoringRI thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #17a2b8 !important;
            color: white;
        }

        .table-wrap::-webkit-scrollbar {
            height: 12px;
        }

        .table-wrap::-webkit-scrollbar-thumb {
            background: #999;
            border-radius: 10px;
        }
    </style>
@stop

@push('scripts')
    <script>
        let tableMonitoringRI;

        const userId = "{{ auth()->id() }}";
        const keyPrefix = "monitoring_ri_" + userId + "_";

        function formatRp(value) {
            return Number(value ?? 0).toLocaleString('id-ID', {
                maximumFractionDigits: 0
            });
        }

        $(function() {

            tableMonitoringRI = $("#tblMonitoringRI").DataTable({
                processing: true,
                stateSave: true,

                stateSaveCallback: function(settings, data) {
                    localStorage.setItem(
                        keyPrefix + "datatable_state",
                        JSON.stringify(data)
                    );
                },

                stateLoadCallback: function(settings) {
                    let savedState = localStorage.getItem(keyPrefix + "datatable_state");
                    return savedState ? JSON.parse(savedState) : null;
                },

                ajax: "{{ route('monitoring.data') }}",

                columns: [{
                        data: 'ID'
                    },
                    {
                        data: 'Nama'
                    },
                    {
                        data: 'PxRS'
                    },
                    {
                        data: 'TGL',
                        render: function(data, type) {
                            if (!data) return '-';

                            let tgl = new Date(data);

                            if (type === 'sort' || type === 'type') {
                                return tgl.getTime();
                            }

                            let dd = String(tgl.getDate()).padStart(2, '0');
                            let mm = String(tgl.getMonth() + 1).padStart(2, '0');
                            let yyyy = tgl.getFullYear();

                            return dd + '/' + mm + '/' + yyyy;
                        }
                    },
                    {
                        data: 'TGL',
                        render: function(data) {

                            if (!data) return '-';

                            let tglMasuk = new Date(data);

                            // buang jam supaya akurat
                            tglMasuk.setHours(0, 0, 0, 0);

                            let hariIni = new Date();
                            hariIni.setHours(0, 0, 0, 0);

                            let selisihHari = Math.floor(
                                (hariIni - tglMasuk) / (1000 * 60 * 60 * 24)
                            );

                            return selisihHari + 1;
                        }
                    },
                    {
                        data: 'Plavon_kls',
                        render: function(data) {
                            return formatRp(data);
                        }
                    },
                    {
                        data: 'Phk3',
                        render: function(data) {
                            return formatRp(data);
                        }
                    },
                    {
                        data: 'Total',
                        render: function(data) {
                            return formatRp(data);
                        }
                    },
                    {
                        data: 'DPJP'
                    },
                    {
                        data: 'RoomName'
                    },
                ],

                responsive: false,
                autoWidth: false,
                scrollX: false,

                paging: true,
                pageLength: 200,

                ordering: true,
                order: [
                    [1, 'desc']
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

            $('#tblMonitoringRI tbody').on('click', 'td', function() {
                const tr = $(this).closest('tr');
                const row = tableMonitoringRI.row(tr);
                const rowData = row.data();

                if (!rowData || !rowData.ID) return;

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    return;
                }

                row.child(`
        <div class="p-3 text-center text-muted">
            <i class="fas fa-spinner fa-spin mr-1"></i>
            Memuat rincian biaya...
        </div>
    `).show();

                tr.addClass('shown');

                $.ajax({
                    url: "{{ url('monitoring/rinci') }}/" + rowData.ID,
                    type: "GET",
                    dataType: "json",
                    success: function(res) {
                        const rincian = [
                            ['Karcis & Jasa', res.karcisJasa],
                            ['Kamar', res.kamar],
                            ['Visit Dokter', res.visit],
                            ['Utilitas', res.utilitas],
                            ['Laboratorium', res.lab],
                            ['Radiologi', res.radiologi],
                            ['Operasi', res.operasi],
                            ['Obat', res.obat],
                            ['Lain-lain', res.lainlain],
                        ];

                        let totalRincian = rincian.reduce((sum, item) => {
                            return sum + Number(item[1] ?? 0);
                        }, 0);

                        let labelRow = rincian.map(item => `
                            <td class="text-center font-weight-bold bg-light">
                                ${item[0]}
                            </td>
                        `).join('');

                        let nominalRow = rincian.map(item => `
                            <td class="text-center">
                                ${formatRp(item[1])}
                            </td>
                        `).join('');

                        let html = `
                            <div class="p-3 bg-light border rounded">
                           
                                <div style="overflow-x:auto;">
                                    <table class="table table-sm table-bordered mb-0 bg-white">
                                        <tbody>
                                            <tr>
                                                ${labelRow}
                                            </tr>
                                            <tr>
                                                ${nominalRow}
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;

                        row.child(html).show();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);

                        row.child(`
                            <div class="alert alert-danger m-2">
                                <strong>Rincian gagal dimuat.</strong><br>
                                Silakan cek route <code>monitoring/rinci/${rowData.ID}</code>
                                atau periksa error pada controller.
                            </div>
                        `).show();
                    }
                });
            });

        });
    </script>
@endpush
