@extends('layouts.admin')

@section('main-content')

<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Data Desa</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button class="btn btn-primary ml-auto" id="btn-add">
                <i class="fas fa-plus-circle"></i>
                Tambah Data
            </button>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Desa</th>
                            <th>Nama Kecamatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center">Data not found</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="user-form">
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="name">Nama Desa <sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan Nama Desa..." autocomplete="off" autofocus>
                            <div class="invalid-feedback" id="valid-name"></div>
                        </div>
                        <div class="form-group">
                            <label for="district_id">Kecamatan</label>
                            <select class="select2 form-control form-control-sm @error('district_id') is-invalid @enderror" name="district_id" id="district_id">
                                <option value="" selected disabled>-- Pilih Kecamatan --</option>
                                @foreach ($districts as $district)
                                <option value="{{ $district->id }}" {{ old('district_id') == $district->name ? 'selected' : '' }}>{{ $district->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="valid-district_id">{{ $errors->first('district_id') }}</div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer no-bd">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Close
                    </button>
                    <button type="button" id="btn-save" class="btn btn-primary">
                        <i class="fas fa-check"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection

@push('scripts')
<!-- JS Libraies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.all.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Setup AJAX CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initializing DataTable
        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: "{{ route('villages.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'district_name',
                    name: 'district_name',
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },

            ]
        });

        // Open Modal to Add new Users
        $('#btn-add').click(function() {
            $('#formModal').modal('show');
            $('.modal-title').html('Tambah Data');
            $('#user-form').trigger('reset');
            $('#btn-save').html('<i class="fas fa-check"></i> Simpan');
            $('#user-form').find('.form-control').removeClass('is-invalid is-valid');
            $('#btn-save').val('save').removeAttr('disabled');
        });

        // Store new grade or update grade
        $('#btn-save').click(function() {
            var formData = {
                name: $('#name').val(),
                district_id: $('#district_id').val(),
            };
            var state = $('#btn-save').val();
            var type = "POST";
            var ajaxurl = "{{ route('villages.store') }}";
            $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Saving...').attr("disabled", true);
            if (state == "update") {
                $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Updating...').attr("disabled", true);
                var id = $('#id').val();
                type = "PUT";
                ajaxurl = "{{ route('villages.index') }}" + '/' + id;
            }
            $.ajax({
                type: type,
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function(data) {
                    if (state == "save") {
                        swal.fire("Berhasil!", 'Data berhasil ditambahkan', "success");
                        $('#dataTable').DataTable().draw(false);
                        $('#dataTable').DataTable().on('draw', function() {
                            $('[data-toggle="tooltip"]').tooltip();
                        });
                    } else {
                        swal.fire("Berhasil!", 'Data berhasil di ubah', "success");
                        $('#dataTable').DataTable().draw(false);
                        $('#dataTable').DataTable().on('draw', function() {
                            $('[data-toggle="tooltip"]').tooltip();
                        });
                    }
                    $('#formModal').modal('hide');
                },
                error: function(data) {
                    try {
                        if (data.responseJSON.errors.employment_number) {
                            $('#employment_number').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-employment_number').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-employment_number').html(data.responseJSON.errors.employment_number);
                        }
                        if (data.responseJSON.errors.name) {
                            $('#name').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-name').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-name').html(data.responseJSON.errors.name);
                        }
                        if (data.responseJSON.errors.position) {
                            $('#position').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-position').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-position').html(data.responseJSON.errors.position);
                        }
                        if (data.responseJSON.errors.email) {
                            $('#email').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-email').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-email').html(data.responseJSON.errors.email);
                        }
                        if (data.responseJSON.errors.password) {
                            $('#password').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-password').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-password').html(data.responseJSON.errors.password);
                        }
                        if (data.responseJSON.errors.grade_id) {
                            $('#grade_id').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-grade_id').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-grade_id').html(data.responseJSON.errors.grade_id);
                        }
                        if (data.responseJSON.errors.role) {
                            $('#role').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-role').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-role').html(data.responseJSON.errors.role);
                        }
                        if (state == "save") {
                            $('#btn-save').html('<i class="fas fa-check"></i> Simpan');
                            $('#btn-save').removeAttr('disabled');
                        } else {
                            $('#btn-save').html('<i class="fas fa-check"></i> Update');
                            $('#btn-save').removeAttr('disabled');
                        }
                    } catch {
                        swal.fire("Maaf!", 'Terjadi kesalahan, Silahkan coba lagi', "error");
                        $('#formModal').modal('hide');
                    }
                }
            });
        });

        // Edit Grade
        $('body').on('click', '#btn-edit', function() {
            var id = $(this).val();
            var name = $(this).data('name');
            $.get("{{ route('villages.index') }}/" + id + '/edit/', function(data) {
                $('#user-form').find('.form-control').removeClass('is-invalid is-valid');
                $('#id').val(data.id);
                $('#name').val(data.name);
                $('#district_id').val(data.district_id);
                $('#btn-save').val('update').removeAttr('disabled');
                $('#formModal').modal('show');
                $('.modal-title').html('Edit Data');
                $('#null').html('<small id="null">Kosongkan jika tidak ingin di ubah</small>');
                $('#btn-save').html('<i class="fas fa-check"></i> Edit');
            }).fail(function() {
                swal.fire("Maaf!", 'Gagal mengambil Data', "error");
            });
        });

        // Delete
        $('body').on('click', '#btn-delete', function() {
            var id = $(this).val();

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-lg btn-primary mr-2',
                    cancelButton: 'btn btn-lg btn-secondary'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Peringatan!',
                text: "Apakah anda yakin?",
                imageWidth: 100,
                imageHeight: 100,
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('villages.index')}}" + '/' + id,
                        success: function(data) {
                            $('#dataTable').DataTable().draw(false);
                            $('#dataTable').DataTable().on('draw', function() {
                                $('[data-toggle="tooltip"]').tooltip();
                            });
                            swal.fire("Berhasil!", 'Data berhasil dihapus', "success");
                        },
                        error: function(data) {
                            swal.fire("Maaf!", 'Terjadi kesalahan, Silahkan coba lagi', "error");
                        }
                    });
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swal.fire("Oh Ya!", 'Data aman, jangan khawatir', "error")
                }
            })


        });

        // Validation on form
        $('body').on('keyup', '#employment_number', function() {
            var test = $(this).val();
            if (test == '') {
                $(this).removeClass('is-valid is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        $('body').on('keyup', '#name', function() {
            var test = $(this).val();
            if (test == '') {
                $(this).removeClass('is-valid is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        $('body').on('keyup', '#position', function() {
            var test = $(this).val();
            if (test == '') {
                $(this).removeClass('is-valid is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        $('body').on('keyup', '#email', function() {
            var test = $(this).val();
            if (test == '') {
                $(this).removeClass('is-valid is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        $('body').on('keyup', '#password', function() {
            var test = $(this).val();
            if (test == '') {
                $(this).removeClass('is-valid is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        $('body').on('change', '#grade_id', function() {
            var test = $(this).val();
            if (test == '') {
                $(this).removeClass('is-valid is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        $('body').on('change', '#role', function() {
            var test = $(this).val();
            if (test == '') {
                $(this).removeClass('is-valid is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
    });
</script>
@endpush