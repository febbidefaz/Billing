@extends('layouts.app')
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('title', 'Rawat Inap')

@section('content_header')

    <div class="d-flex align-items-center">

        <h1 class="mb-0 mr-2">
            Data Rawat Inap
        </h1>

        <span class="badge badge-info shadow-sm px-3 py-2" id="totalPasienHeader"
            style="
        font-size:14px;
        font-weight:1000;
        border-radius:10px;
        letter-spacing:0.5px;
        ">

            0 Pasien

        </span>

    </div>

@stop

@section('page-content')

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-wrap">
                <table id="tbl" class="table table-hover table-striped table-bordered nowrap">
                    <thead class="bg-info">
                        <tr>
                            <th style="width:40px">PxRS</th>
                            <th style="width:40px">ID</th>
                            <th style="width:40px">NoRM</th>
                            <th style="width:50px">Nama Pasien</th>
                            <th style="width:50px">Ruang</th>
                            <th style="width:50px">T.Masuk</th>
                            <th style="width:60px">NoSEP</th>
                            <th style="width:10px">Kls</th>
                            <th style="width:100px">Alamat</th>
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

        /* KUNCI LEBAR TABEL */
        #tbl {
            min-width: 800px !important;
            width: max-content !important;
        }

        /* Supaya kolom tidak turun */
        #tbl th,
        #tbl td {
            white-space: nowrap;
            vertical-align: middle;
        }

        /* Header tabel sticky */
        #tbl thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #17a2b8 !important;
            color: white;
        }

        /* Scroll horizontal lebih halus */
        .table-wrap::-webkit-scrollbar {
            height: 12px;
        }

        .table-wrap::-webkit-scrollbar-thumb {
            background: #999;
            border-radius: 10px;
        }
    </style>

@stop

@section('js')

    <script>
        $(function() {
            $("#tbl").DataTable({

                processing: true,
                ajax: "{{ route('rawatinap.data') }}",

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
                        data: 'TIN',
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
                        data: 'NoSEP'
                    },
                    {
                        data: 'Plavon_kls'
                    },
                    {
                        data: 'Addr'
                    }
                ],

                createdRow: function(row, data) {
                    $(row).css('cursor', 'pointer');

                    $(row).on('click', function() {
                        window.location.href = '/rawat-inap/' + data.ID;
                    });
                },
                responsive: false,
                autoWidth: false,

                /* MATIKAN scrollX bawaan DT */
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
        });
    </script>

@stop
