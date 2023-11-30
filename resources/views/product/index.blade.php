@extends('layouts.app',['title' => 'Product'])

@section('breadcrumb')
    <div class="col-sm-6">
        <h1 class="m-0">Product</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Product</li>
        </ol>
    </div>
@endsection

@section('content')
<div class="px-2">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-store mr-1"></i>
                Product
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                    data-target="#product-create">Add New</button>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered" id="productTableId">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Service Name</th>
                        <th>description</th>
                        <th>Prodcut Key</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@include('product.create')
@include('product.edit')
@endsection

{{-- scripts --}}

@push('scripts')
    <script>
        $(function() {

            url = '/product';
            table = $('#productTableId').DataTable({
                processing: true,
                serverSide: true,
                ajax: url,
                ordering: false,
                columns: [{
                        render: function(data, type, row) {
                            return row.name;
                        },
                        targets: 0,
                    },
                    {
                        render: function(data, type, row) {
                            return row.service.name;
                        },
                        targets: 0,
                    },
                    {
                        render: function(data, type, row) {
                            return row.description;
                        },
                        targets: 0,
                    },
                    {
                        render: function(data, type, row) {
                            return row.product_key;
                        },
                        targets: 0,
                    },
                    {
                        render: function(data, type, row) {
                            const btns = `
                            <div class="btn-group" id="${row.id}">

                                <button type="button" class="btn btn-outline-info productEditBtn" data-toggle="modal"
                                    data-target="#product-edit">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger productDeleteBtn">
                                    <i class="fa-solid fa-trash"></i>
                                    </button>
                            </div>
                            `;

                            return btns;
                        },
                        targets: 0,
                    },
                ]
            });
            productEditBtnHandler();
            productDeleteBtnHandler();

        });

        const productEditBtnHandler = () => {
            $(document).on('click', '.productEditBtn', function() {
                const id = $(this).parent().attr('id');
                axios.get(`/product/${id}/edit`)
                .then(function(response) {
                    const data = response.data.data;
                    console.log(data);
                    $("#productUpdateFrom").attr('action', `/product/${id}`);
                    $("#productUpdateFrom #update_name").val(data.name);
                    $("#productUpdateFrom #update_service_id").val(data.service_id);

                    $("#productUpdateFrom #update_keyword").val(data.product_key);
                    $("#productUpdateFrom #update_description").val(data.description);
                    $("#product-update").modal('show');
                });
            });
        };

        const productDeleteBtnHandler = () => {
            $(document).on('click', '.productDeleteBtn', function() {
                const id = $(this).parent().attr('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(`/product/${id}`)
                            .then(function(response) {
                                table.ajax.reload();
                            })
                            .catch(function(error) {
                                console.log(error);
                            });
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                    }else{
                        Swal.fire(
                            'Cancelled!',
                            'Your file is safe.',
                            'error'
                        )
                    }
                })
            });
        };
    </script>
@endpush
