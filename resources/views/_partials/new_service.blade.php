<div class="login-box">
    <div class="card">
        <div class="card-body login-card-body">
            <div class="login-text">
                Choice your service
            </div>        
            <form method="POST" action="{{ route('service.subscription') }}">
                @csrf
                <div class="form-group">
                    <label class="required">Phone Number</label>
                    <input type="text" required class="form-control" name="phone_number" placeholder="+8801700000000"/>
                </div>
                <div class="form-group">
                    <label class="required">Select a service</label>
                    <select class="form-control" name="service_id">
                        <option value="" disable selected>
                            Select a service    
                        </option>
                        @foreach ($services as $service)
                            <option value="{{$service->id}}">{{$service->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <button type="submit" class="btn btn-primary w-full">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>