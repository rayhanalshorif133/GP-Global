@extends('layouts.app', ['title' => 'Service'])
@section('breadcrumb')
    <div class="col-sm-6">
        <h1 class="m-0">Service</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">dashboard</a></li>
            <li class="breadcrumb-item active">Service</li>
        </ol>
    </div>
@endsection

@section('content')
    <div class="px-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-hammer mr-1"></i>
                    Service
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                        data-target="#service-create">Add New</button>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered" id="serviceTableId">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>keyword</th>
                            <th>Validity</th>
                            <th>Charge</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    @include('service.create')
    @include('service.edit')
    @include('service.show')
@endsection

{{-- scripts --}}
@push('scripts')
    <script>
        $(function() {

            url = '/service';
            table = $('#serviceTableId').DataTable({
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
                            return row.type;
                        },
                        targets: 0,
                    },
                    {
                        render: function(data, type, row) {
                            return row.keyword;
                        },
                        targets: 0,
                    },
                    {
                        render: function(data, type, row) {
                            var validity = row.validity == 'P1D' ? "Daily" : row.validity == 'P7D' ? "Weekly" : "Monthly";
                            return validity;
                        },
                        targets: 0,
                    },
                    {
                        render: function(data, type, row) {
                            return `<span>${row.amount} tk</span>`;
                        },
                        targets: 0,
                    },
                    {
                        render: function(data, type, row) {
                            const btns = `
                                <div class="btn-group" id="${row.id}">
                                        <button type="button" class="btn btn-sm btn-outline-success serviceShowBtn" data-toggle="modal"
                                        data-target="#service-show">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info serviceEditBtn" data-toggle="modal"
                                        data-target="#service-edit">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger serviceDeleteBtn">
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
            serviceEditBtnHandler();
            serviceDeleteBtnHandler();
            serviceShowBtnHandaler();

            // get url
            var searchValue = window.location.href?.split('?')[1]?.split('=')[1] ? window.location.href?.split('?')[1]?.split('=')[1] : '';
            $('#serviceTableId_filter input').val(searchValue);
            table.search(searchValue).draw();
        });

        const serviceEditBtnHandler = () => {
            $(document).on('click', '.serviceEditBtn', function() {
                const id = $(this).parent().attr('id');
                axios.get(`/service/${id}/edit`)
                .then(function(response) {
                    const data = response.data.data;
                    $("#serviceUpdateFrom").attr('action', `/service/${id}`);
                    const charge = parseFloat(data.charge);
                    $("#serviceUpdateFrom input[name='name']").val(data.name);
                    $("#serviceUpdateFrom select[name='type']").val(data.type);
                    $("#serviceUpdateFrom select[name='validity']").val(data.validity);
                    $("#updateCharge").val(charge);
                    $("#serviceUpdateFrom input[name='purchase_category_code']").val(data.purchase_category_code);
                    $("#serviceUpdateFrom input[name='reference_code']").val(data.reference_code);
                    $("#serviceUpdateFrom input[name='channel']").val(data.channel);
                    $("#serviceUpdateFrom input[name='amount']").val(data.amount);
                    $("#serviceUpdateFrom input[name='product_id']").val(data.productId);
                    $("#serviceUpdateFrom textarea[name='description']").val(data.description);
                    $("#serviceUpdateFrom input[name='on_behalf_of']").val(data.on_behalf_of);
                    $("#serviceUpdateFrom input[name='redirect_url']").val(data.redirect_url);
                    $("#serviceUpdateFrom input[name='portal_link']").val(data.portal_link);
                    $("#serviceUpdateFrom input[name='renewal_notification_api']").val(data.renewal_notification_api);
                    $("#serviceUpdateFrom input[name='notification_url']").val(data.notification_url);
                    $("#serviceUpdateFrom input[name='keyword']").val(data.keyword);
                    $("#service-update").modal('show');
                });
            });
        };

        const serviceShowBtnHandaler = () => {
            $(document).on('click', '.serviceShowBtn', function() {
                const id = $(this).parent().attr('id');
                axios.get(`/service/${id}`)
                .then(function(response) {
                    const data = response.data.data;
                    console.log(data);
                    $(".show_service_name").text(data.name);
                    $(".show_service_type").text(data.type);
                    $(".show_service_validity").text(data.validity);
                    $(".show_service_charge").text(data.amount);
                    const channel = data.channel? data.channel : "N/A";
                    const reference_code = data.reference_code? data.reference_code : "N/A";
                    $(".show_reference_code").text(reference_code);
                    $(".show_channel").text(channel);
                    const portal_link = data.portal_link? data.portal_link : "N/A";
                    $(".show_portal_link").text(portal_link);
                    $(".show_redirect_url").text(data.redirect_url);
                    $(".show_notification_url").text(data.notification_url);
                    $(".show_renewal_notification_api").text(data.renewal_notification_api);
                    $(".show_productId").text(data.productId);
                    const des = data.description ? data.description : "N/A";
                    $(".show_description").text(des);
                    $("#service-show").modal('show');
                });
            });
        };
        const serviceDeleteBtnHandler = () => {
            $(document).on('click', '.serviceDeleteBtn', function() {
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
                        axios.delete(`/service/${id}`)
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
