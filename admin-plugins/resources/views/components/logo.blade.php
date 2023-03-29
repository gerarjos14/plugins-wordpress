@push('css')
    @if (auth()->user()->isCustomer())
        @if (auth()->user()->agency->image)
            <style>
                aside.main-sidebar a.brand-link{
                    padding: 8px;
                }
                aside.main-sidebar a.brand-link span.brand-text{
                    display: none;
                }
            </style>
        @endif()
    @elseif(auth()->user()->isAgency())
        @if (auth()->user()->image)
            <style>
                aside.main-sidebar a.brand-link{
                    padding: 8px;
                }
                aside.main-sidebar a.brand-link span.brand-text{
                    display: none;
                }
            </style>
        @endif
    @endif
@endpush    
@push('js')
    @if (auth()->user()->isCustomer())
        @if (auth()->user()->agency->image)
            <script>
                jQuery(document).ready(function() {
                    let url = "{{asset(auth()->user()->image)}}";      
                    $('aside.main-sidebar a.brand-link img').attr("src",url);
                });
            </script>
        @endif
    @elseif(auth()->user()->isAgency())
        @if (auth()->user()->image)
            <script>
                jQuery(document).ready(function() {
                    let url = "{{asset(auth()->user()->image)}}";      
                    $('aside.main-sidebar a.brand-link img').attr("src",url);
                });
            </script>  
        @endif
    @endif    
@endpush