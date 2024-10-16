<div class="login-box">
    <div class="card">
        <div class="card-body login-card-body">
            <div class="login-text">
                Refund
            </div>        
            <form method="POST" action="{{ route('service.refund') }}">
                @csrf
                <div class="form-group">
                    <label class="required">ACR Key</label>
                    <input type="text" required class="form-control" name="acr_key" placeholder="Enter a acr key"/>
                </div>
                <div class="row">
                    <button type="submit" class="btn btn-primary w-full">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>