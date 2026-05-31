@extends('layouts.app')
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('title', 'IGD')

@section('content_header')

    <div class="d-flex align-items-center">

        <h1 class="mb-0 mr-2">
            Data Pasien IGD
        </h1>

        <span class="badge badge-info shadow-sm px-3 py-2" id="totalPasienHeader"
            style="font-size:14px;font-weight:1000;border-radius:10px;letter-spacing:0.5px;">
            0 Pasien
        </span>

    </div>

@stop

@section('page-content')

    <div class="card shadow-sm">

        <div class="card-header bg-light">

            <div class="row align-items-end">

                <div class="col-md-3">
                    <label>Tanggal Awal</label>
                    <input type="date" id="tglAwal" class="form-control" value="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-3">
                    <label>Tanggal Akhir</label>
                    <input type="date" id="tglAkhir" class="form-control" value="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-info btn-block" onclick="reloadIGD()">
                        <i class="fas fa-search"></i>
                        Tampilkan
                    </button>
                </div>

            </div>

        </div>

        <div class="card-body p-0">

            <div class="table-wrap">
                <table id="tblIGD" class="table table-hover table-striped table-bordered nowrap">
                    <thead class="bg-info">
                        <tr>
                            <th>PxRS</th>
                            <th>ID</th>
                            <th>NoRM</th>
                            <th>Nama Pasien</th>
                            <th>Dokter</th>
                            <th>Tanggal</th>
                            <th>No SEP</th>
                            <th>Alamat</th>
                            <th>Follow Up</th>
                            <th>Sub Layanan</th>

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

        #tblIGD {
            min-width: 900px !important;
            width: max-content !important;
        }

        #tblIGD th,
        #tblIGD td {
            white-space: nowrap;
            vertical-align: middle;
        }

        #tblIGD thead th {
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

@section('js')

    <script>
        let tableIGD;

        $(function() {

            tableIGD = $("#tblIGD").DataTable({

                processing: true,

                ajax: {
                    url: "{{ route('igd.data') }}",
                    data: function(d) {
                        d.tgl_awal = $('#tglAwal').val();
                        d.tgl_akhir = $('#tglAkhir').val();
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
                        data: 'Dokter'
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
                        data: 'NoSEP'
                    },
                    {
                        data: 'Addr'
                    },
                    {
                        data: 'FollowUp'
                    },
                    {
                        data: 'SubLayanan'
                    }

                ],

                createdRow: function(row, data) {
                    $(row).css('cursor', 'pointer');

                    $(row).on('click', function() {
                        window.location.href = '/igd/' + data.ID;
                    });
                },

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

        });

        function reloadIGD() {
            tableIGD.ajax.reload();
        }
    </script>

@stop
