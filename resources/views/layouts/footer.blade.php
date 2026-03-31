<footer class="main-footer">
    <div class="float-right d-none d-sm-block">
        <b>Version</b> 3.5.1
    </div>
    <strong>Copyright &copy; 2014-<?=date('Y')?> <a href="#">IBSTEC</a>.</strong> All rights
    reserved.
</footer>

<!-- Toastr JS -->
<script src="{{asset('dist/js/jquery.min.js')}}"></script>
<script src="{{asset('dist/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('dist/js/jquery.overlayScrollbars.min.js')}}"></script>
<script src="{{asset('dist/js/demo.js')}}"></script>

<!-- Include the DateRangePicker library -->
<link href="{{asset('/dist/css/daterangepicker.css')}}" rel="stylesheet">
<script src="{{asset('/dist/js/moment.min.js')}}"></script>
<script src="{{asset('/dist/js/daterangepicker.min.js')}}"></script>
<script src="{{asset('/dist/js/dataTables.min.js')}}"></script>
<script src="{{asset('/dist/js/select2.full.min.js')}}"></script>
<script src="{{ asset('dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('dist/js/toastr.min.js') }}"></script>

{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css"> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script> --}}
<script>
    // Allow only valid decimal or integer numbers
    $('.rateInput').on('input', function() {
        var value = $(this).val();

        // Regular expression for valid decimal or integer numbers
        var isValidNumber = /^(\d+(\.\d*)?|\.\d+)$/.test(value);

        // Validate if value is a valid number and doesn't contain spaces
        if (!isValidNumber || value.includes(' ')) {
            $(this).next('.text-danger').text('Please enter a valid number.');
        } else {
            $(this).next('.text-danger').text('');
        }
    });

    $(document).ready(function () {
    // Initialize the treeview functionality
    $('.nav-item.has-treeview').on('click', function (e) {
        // Prevent the default action only for dropdown toggles, not for links
        if (!$(e.target).closest('a').length) {
            e.preventDefault();
        }
        $(this).toggleClass('menu-open');
    });
});

</script>