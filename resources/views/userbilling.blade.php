@extends('adminlte::page')

@section('title', 'User Billing')

@section('content_header')
    <h1>User Billing</h1>
@stop
@section('js')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(function() {
            $('#tableUserBilling').DataTable({
                responsive: true,
                autoWidth: false,
                ordering: true,
                searching: true,
                paging: true,
                pageLength: 10,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });
        });
    </script>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-2 text-left">
        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalTambahUser">
            <i class="fas fa-user-plus"></i>
            Tambah User
        </button>
    </div>

    {{-- Modal Tambah User --}}
    <div class="modal fade" id="modalTambahUser" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-user-plus mr-2"></i>
                        Tambah User Billing
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('userbilling.store') }}">
                    @csrf

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="Nama" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="Username" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="Password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <select name="Role" class="form-control" required>
                                <option value="admin">Admin</option>
                                <option value="kasir">Kasir</option>
                                <option value="casemix">Casemix</option>
                                <option value="perawat">Perawat</option>
                                <option value="user">User</option>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label>Aktif</label>
                            <select name="Aktif" class="form-control">
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-info btn-sm">
                            <i class="fas fa-save"></i>
                            Simpan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- Tabel User --}}
    <div class="card">


        <div class="card-body table-responsive">
            <table id="tableUserBilling" class="table table-bordered table-striped">
                <thead class="bg-info">
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aktif</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($users as $u)
                        <tr>
                            <td>{{ $u->Nama }}</td>
                            <td>{{ $u->Username }}</td>
                            <td>{{ $u->Role }}</td>
                            <td>
                                @if ($u->Aktif == 1)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                    data-target="#modalEditUser{{ $u->ID }}">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                            </td>
                        </tr>

                        {{-- Form Hapus User --}}
                        <form id="deleteUser{{ $u->ID }}" action="{{ route('userbilling.destroy', $u->ID) }}"
                            method="POST" style="display:none;">
                            @csrf
                            @method('DELETE')
                        </form>

                        {{-- Modal Edit User --}}
                        <div class="modal fade" id="modalEditUser{{ $u->ID }}" tabindex="-1">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content border-0 shadow-lg">

                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title font-weight-bold">
                                            <i class="fas fa-user-edit mr-2"></i>
                                            Edit User Billing
                                        </h5>

                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <form method="POST" action="{{ route('userbilling.update', $u->ID) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-body">

                                            <div class="form-group">
                                                <label>Nama</label>
                                                <input type="text" name="Nama" class="form-control form-control-sm"
                                                    value="{{ $u->Nama }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" name="Username" class="form-control form-control-sm"
                                                    value="{{ $u->Username }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Password</label>
                                                <input type="password" name="Password" class="form-control form-control-sm"
                                                    placeholder="Kosongkan jika tidak diganti">
                                            </div>

                                            <div class="form-group">
                                                <label>Role</label>
                                                <select name="Role" class="form-control form-control-sm" required>
                                                    <option value="admin" {{ $u->Role == 'admin' ? 'selected' : '' }}>
                                                        Admin
                                                    </option>

                                                    <option value="kasir" {{ $u->Role == 'kasir' ? 'selected' : '' }}>
                                                        Kasir
                                                    </option>

                                                    <option value="casemix" {{ $u->Role == 'casemix' ? 'selected' : '' }}>
                                                        Casemix
                                                    </option>

                                                    <option value="perawat" {{ $u->Role == 'perawat' ? 'selected' : '' }}>
                                                        Perawat
                                                    </option>

                                                    <option value="user" {{ $u->Role == 'user' ? 'selected' : '' }}>
                                                        User
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label>Aktif</label>
                                                <select name="Aktif" class="form-control form-control-sm">
                                                    <option value="1" {{ $u->Aktif == 1 ? 'selected' : '' }}>
                                                        Ya
                                                    </option>

                                                    <option value="0" {{ $u->Aktif == 0 ? 'selected' : '' }}>
                                                        Tidak
                                                    </option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">

                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="if(confirm('Yakin ingin menghapus user ini?')) document.getElementById('deleteUser{{ $u->ID }}').submit();">
                                                <i class="fas fa-trash"></i>
                                                Hapus
                                            </button>

                                            <div>
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    data-dismiss="modal">
                                                    Batal
                                                </button>

                                                <button type="submit" class="btn btn-info btn-sm">
                                                    <i class="fas fa-save"></i>
                                                    Update
                                                </button>
                                            </div>

                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

@stop
