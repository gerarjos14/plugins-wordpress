@if(session('message'))
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-{{ session('message')[0] }}">
                <p class="m-0">
                    {{ session('message')[1] }}
                    @if (session('btn_nex_pass'))
                        <a href="{{url('/subscriptions')}}" class="btn btn-primary" style="text-decoration: none !important;">{{session('btn_nex_pass')}}</a>
                    @endif
                </p>
                
            </div>
        </div>
    </div>
</div>
@endif
