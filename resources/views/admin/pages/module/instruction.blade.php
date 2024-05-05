@extends('admin.layout.master')
@section('title')
   Module Instruction
@endsection
@section('content')
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Module</strong> Instruction</h1>
            <div class="row ">
                <div class="col-xl-6 col-xxl-5 ">
                    <div class="w-100">
                        <div class="row">
                            <div class="container mt-5 mb-5">
                                <div class="row">
                                    <div class="col-12">
                                        <h4>{{ucfirst($name)}}</h4>
                                        <ul class="timeline">
                                            <li>
                                                <p class="text-primary fw-bold "> <span style="color: green"><i class="fa-solid fa-check-double"></i> </span> Route</p>
                                                <p> New Route Registered on <span class="text-success">routes/Backend.php</span> </p>
                                            </li>
                                            <li>
                                                <p class="text-primary fw-bold "><span style="color: green"><i class="fa-solid fa-check-double"></i> </span> Controller</p>
                                                <p> New controller file added in <span class="text-success">app/http/controller/Backend/{{ucfirst($name)}}Controller.php</span></p>
                                                <p> Necessary code added for complete Crud operation.</p>
                                                <p> Controller File extends BaseController which have all functionality of controller and added functionality. <span class="text-danger fw-bold">Don't change it.<i class="fa-solid fa-x"></i></span></p>
                                            </li>
                                            <li>
                                                <p class="text-primary fw-bold "> <span style="color: green"><i class="fa-solid fa-check-double"></i> </span> Model</p>
                                                <p>Model created with  <span class="text-success">mandatory </span> relationship.</p>
                                            </li>
                                            <li>
                                                <p class="text-primary fw-bold "><span style="color: green"><i class="fa-solid fa-check-double"></i> </span> Migration</p>
                                                <p>Migration created with  <span class="text-success fw-bold">schema </span>  builder.</p>
                                                <p class="text-danger fw-bold">Don't remove or modify mandatory field.<i class="fa-solid fa-x"></i> </p>
                                            </li>
                                            <li >
                                                <p class="text-primary fw-bold "><span style="color: green"><i class="fa-solid fa-check-double"></i> </span> View</p>
                                                <p> View file locate in  <span class="text-success fw-bold">resources/views/admin/pages/Modules/{{ucfirst($name)}}/index.blade.php </span> </p>
                                            </li>
                                            <li >
                                                <p class="text-primary fw-bold "><span style="color: green"><i class="fa-solid fa-check-double"></i> </span> Udpate change</p>
                                                <p> Visit to <a href="{{ route('system.update') }}" target="_blank" style="color: green" >/admin/system-update</a> </p>
                                                <p> then <a href="{{ route('cache.clear') }}" target="_blank" style="color: green">/clear</a> </p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('script')
    <script>

    </script>
@endsection