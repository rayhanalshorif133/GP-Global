<div class="login-box">
    <div class="card">
        <div class="card-body login-card-body">
            <div class="login-text">
                Send SMS
            </div>        
            <form method="POST" action="{{ route('partner.smsmessaging.send-sms.web') }}">
                @csrf
                <div class="form-group">
                    <label class="required">ACR Key</label>
                    <input type="text" required class="form-control" name="acr_key" placeholder="Enter a acr key"/>
                </div>
                <div class="form-group">
                    <label class="required">Sender Phone Number</label>
                    <input type="text" required class="form-control" name="phone_number" placeholder="+8801700000000"/>
                </div>
                <div class="form-group">
                    <label class="required">Text</label>
                    <textarea type="text" required class="form-control" name="msg" placeholder="Enter your message"></textarea>
                </div>
                <div class="row">
                    <button type="submit" class="btn btn-primary w-full">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>