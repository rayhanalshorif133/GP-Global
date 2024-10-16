<div class="modal fade" id="service-edit" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-hammer mr-1"></i>
                    Update Service
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="#" method="POST" id="serviceUpdateFrom">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="px-2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="required">Service Name</label>
                                    <input type="text" name="name" id="name" required class="form-control"
                                        placeholder="Enter Service Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type" class="required">Service Type</label>
                                    <select class="form-control" name="type" required id="type">
                                        <option value="" selected disabled>Select type</option>
                                        <option value="subscription">Subscription</option>
                                        <option value="on-demand">On Demand</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="validity" class="required">Service validity</label>
                                    <select class="form-control" name="validity" required id="validity">
                                        <option value="" selected disabled>Select validity</option>
                                        <option value="P1D">Daily (P1D)</option>
                                        <option value="P7D">Weekly (P7D)</option>
                                        <option value="P30D">Monthly (P30D)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="updateAmount" class="required">Service amount</label>
                                    <input type="number" name="amount" id="updateAmount" required class="form-control"
                                        placeholder="Enter Service amount">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="keyword" class="required">Keyword</label>
                                    <input type="text" name="keyword" id="keyword" required class="form-control"
                                        placeholder="Enter Keyword Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_id" class="required">Product ID</label>
                                    <input type="text" name="product_id" id="product_id" required class="form-control"
                                        placeholder="Enter product ID">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="redirect_url" class="required">Redirect URL</label>
                                    <input type="text" name="redirect_url" id="redirect_url" required class="form-control"
                                        placeholder="Enter Redirect URL">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="portal_link" class="optional">Portal Link</label>
                                    <input type="text" name="portal_link" id="portal_link" class="form-control"
                                        placeholder="Enter portal link">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notification_url" class="optional">Notification Url</label>
                                    <input type="text" name="notification_url" id="notification_url" class="form-control"
                                        placeholder="Enter notification url">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="renewal_notification_api" class="optional">Renewal Notification Api</label>
                                    <input type="text" name="renewal_notification_api" id="renewal_notification_api" class="form-control"
                                        placeholder="Enter Renewal Notification Api">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="optional">Description</label>
                                    <textarea type="text" name="description" id="description" class="form-control"
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
