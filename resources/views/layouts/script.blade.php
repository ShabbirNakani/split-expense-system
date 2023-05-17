<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
{{-- <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E="
    crossorigin="anonymous"></script> --}}
<!-- Bootstrap -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>

<!-- OPTIONAL SCRIPTS -->
<script src={{ asset('plugins/chart.js/Chart.min.js') }}></script>
<!-- AdminLTE for demo purposes -->
{{-- <script src={{ asset('dist/js/demo.js') }}></script> --}}
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
{{-- <script src={{ asset('dist/js/pages/dashboard3.js') }}></script> --}}

<script src="{{ asset('js/custom.js') }}"></script>

{{-- select to cdn --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.js"></script>

{{-- tostr cdn --}}
<script src="../../plugins/toastr/toastr.min.js"></script>

{{-- Sweet alert cdn --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>

{{-- ajax headers --}}
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'csrftoken': '{{ csrf_token() }}'
        }
    });
</script>
