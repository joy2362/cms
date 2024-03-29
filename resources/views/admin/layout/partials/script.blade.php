    <script src="{{asset('asset/js/jquery-3.6.0.min.js')}}"></script>
    @vite('resources/js/app.js')
    <script src="{{asset('asset/js/app.js')}}"></script>
    <script src="{{asset('asset/js/custom.js')}}"></script>
    <script src="{{asset('asset/js/datatables.min.js')}}"></script>
    <script src="{{asset('asset/js/toastr.min.js')}}"></script>
    <script src="{{asset('asset/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('asset/js/additional-methods.min.js')}}"></script>
    <script src="//cdn.ckeditor.com/4.5.9/standard/ckeditor.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/ckeditor/4.5.9/adapters/jquery.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        @if(Session::has('status'))
            notification('success','{{Session::get('status')}}')
        @endif

        @if(Session::has('messege'))
            const type = "{{Session::get('alert-type','info')}}";
            notification(type,'{{Session::get('messege')}}')
        @php
            Illuminate\Support\Facades\Session::forget('messege');
        @endphp
        @endif

    });
</script>