@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('title', 'Rawat Jalan')

@section('content_header')
    <div class="d-flex align-items-center">
        <h1 class="mb-0 mr-2">Data Rawat Jalan</h1>

        <span class="badge badge-info shadow-sm px-3 py-2" id="totalPasienHeader"
            style="font-size:16px; font-weight:700; border-radius:10px;">
            0 Pasien
        </span>
    </div>
    <div class="row mb-2">

        <div class="col-md-2">
            <input type="date" id="tgl1" class="form-control" value="{{ date('Y-m-d') }}">
        </div>

        <div class="col-md-2">
            <input type="date" id="tgl2" class="form-control" value="{{ date('Y-m-d') }}">
        </div>

        <div class="col-md-2">
            <button id="btnFilter" class="btn btn-info">
                Filter
            </button>
        </div>

    </div>
@stop

@section('content')

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-wrap">
                <table id="tblRawatJalan" class="table table-hover table-striped table-bordered nowrap">
                    <thead class="bg-info">
                        <tr>
                            <th style="width:50px">PxRS</th>
                            <th style="width:40px">ID</th>
                            <th style="width:40px">NoRM</th>
                            <th style="width:50px">Nama Pasien</th>
                            <th style="width:20px">Tanggal</th>
                            <th style="width:80px">Alamat</th>
                            <th style="width:50px">Bagian</th>
                            <th style="width:10px">Shift</th>
                            <th style="width:50px">NoSEP</th>
                            <th style="width:50px">No JKN</th>
                            <th style="width:40px">No WA</th>

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

        #tblRawatJalan {
            min-width: 450px !important;
            table-layout: auto !important;
        }

        #tblRawatJalan th,
        #tblRawatJalan td {
            white-space: nowrap;
            vertical-align: middle;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #tblRawatJalan thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #17a2b8 !important;
            color: white;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            let table = $("#tblRawatJalan").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('rawatjalan.data') }}",
                    data: function(d) {
                        d.tgl_awal = $('#tgl1').val();
                        d.tgl_akhir = $('#tgl2').val();
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
                        render: function(data) {
                            if (!data) return '-';

                            let tgl = new Date(data);
                            let dd = String(tgl.getDate()).padStart(2, '0');
                            let mm = String(tgl.getMonth() + 1).padStart(2, '0');
                            let yyyy = tgl.getFullYear();

                            return dd + '/' + mm + '/' + yyyy;
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
                    },

                ],

                createdRow: function(row, data) {

                    $(row).css('cursor', 'pointer');

                    $(row).on('click', function() {

                        window.location.href =
                            "{{ route('rawatjalan.detail', ':id') }}".replace(':id', data.ID);

                    });

                },

                responsive: false,
                autoWidth: false,
                scrollX: false,
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
                    let total = settings.fnRecordsTotal();
                    $('#totalPasienHeader').html(total + ' Pasien');
                },

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

            $('#btnFilter').on('click', function() {
                table.ajax.reload();
            });
        });
    </script>
@stop
