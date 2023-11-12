<div class="modal fade" id="product-edit" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-hammer mr-1"></i>
                    Create New Product
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="#" method="POST" enctype="multipart/form-data" id="productUpdateFrom">
                @csrf
                <div class="modal-body">
                    <div class="px-2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="required">Product Name</label>
                                    <input type="text" name="name" id="update_name" required class="form-control"
                                        placeholder="Enter Product Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="service_id" class="required">Select a Service</label>
                                    <select class="form-control" name="service_id" required id="update_service_id">
                                        @foreach($services as $service)
                                            <option value="{{$service->id}}">{{$service->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="keyword" class="required">Keyword</label>
                                    <input type="text" name="product_key" id="update_keyword" required class="form-control"
                                        placeholder="Enter Keyword Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description" class="optional">Description</label>
                                    <textarea type="text" name="description" id="update_description" class="form-control"
                                        placeholder="Enter description Name"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>

    </div>

</div>
