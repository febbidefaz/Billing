@extends('adminlte::page')

@section('content')
    @yield('page-content')

    {{-- Modal Cari --}}
    <div class="modal fade" id="modalCariPasien" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Cari Pasien</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="text" id="cariPasien" class="form-control" placeholder="Ketikkan ID pasien">

                    <div id="hasilCariPasien" class="mt-3"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>

                    <button type="button" class="btn btn-primary" onclick="cariPasien()">
                        Cari
                    </button>
                </div>

            </div>
        </div>
    </div>
@stop

@section('css')
    @yield('css')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@stop

@section('js')
    @stack('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: "2000"
        };
    </script>

    <script>
        function cariPasien() {

            let id = $('#cariPasien').val();

            if (id == '') {
                alert('Masukkan ID Pasien');
                return;
            }

            $.ajax({
                url: "{{ route('cari.pasien.id') }}",
                type: "GET",
                data: {
                    id: id
                },
                success: function(res) {
                    let p = res.data;
                    let urlRawatInap = "{{ route('rawatinap.detail', ':id') }}".replace(':id', p.ID);
                    let urlRawatJalan = "{{ route('rawatjalan.detail', ':id') }}".replace(':id', p.ID);
                    let urlIgd = "{{ route('igd.detail', ':id') }}".replace(':id', p.ID);


                    $('#hasilCariPasien').html(`
                        <table class="table table-bordered table-sm mt-3">
                            <tr><th>ID</th><td>${p.ID}</td></tr>
                            <tr><th>RM</th><td>${p.RegNum}</td></tr>
                            <tr><th>Nama</th><td>${p.Nama}</td></tr>
                            <tr><th>Alamat</th><td>${p.Addr ?? '-'}</td></tr>
                            <tr><th>Gender</th><td>${p.Jenis_Kelamin == 'P' ? 'Perempuan' : 'Laki-laki'}</td></tr>
                            <tr><th>Tanggal Lahir</th><td>${p.Tanggal_Lahir ? p.Tanggal_Lahir.substring(0,10) : '-'}</td></tr>
                            <tr><th>Layanan</th><td>${p.Layanan ?? '-'}</td></tr>
                            <tr><th>Spesialis</th><td>${p.SubLayanan ?? '-'}</td></tr>
                            <tr><th>Status</th><td>${p.FollowUp ?? '-'}</td></tr>
                        </table>

                        <div class="mt-3">
                            <a href="${urlRawatInap}" class="btn btn-success">
                                <i class="fas fa-bed"></i> Rawat Inap
                            </a>

                            <a href="${urlRawatJalan}" class="btn btn-warning">
                                <i class="fas fa-book"></i> Rawat Jalan
                            </a>

                            <a href="${urlIgd}" class="btn btn-danger">
                                <i class="fas fa-ambulance"></i> IGD
                            </a>
                        </div>
                    `);
                }
            });
        }
    </script>
@stop
