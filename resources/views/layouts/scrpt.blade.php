<!-- scrpt.blade.php -->
<!--   Core JS Files   -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js" integrity="sha512-2rNj2KJ+D8s1ceNasTIex6z4HWyOnEYLVC3FigGOmyQCZc2eBXKgOxQmo3oKLHyfcj53uz4QMsRCWNbLd32Q1g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="{{ asset('assets/js/plugins/bootstrap-switch.js') }}"></script>
{{-- <!  Chartist Plugin  >
<script src="{{ asset('assets/js/plugins/chartist.min.js') }}"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.4/chartist.min.js"></script> --}}
<!--  Notifications Plugin    -->
<script src="{{ asset('assets/js/plugins/bootstrap-notify.js') }}"></script>
<!-- Control Center for Light Bootstrap Dashboard: scripts for the example pages etc -->
<script src="{{ asset('assets/js/light-bootstrap-dashboard.js?v=2.0.0 ') }}" type="text/javascript"></script>
<!-- Light Bootstrap Dashboard DEMO methods, don't include it in your project! -->
<script src="{{ asset('assets/js/demo.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-fixedcolumns/js/dataTables.fixedColumns.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
{{--  Scripts  --}}


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
@yield('scriptss')
@stack('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        // Javascript method's body can be found in assets/js/demos.js
        // demo.initDashboardPageCharts();

        // demo.showNotification();

    });
</script>


<script>
  function confirmLogout() {
    Swal.fire({
      title: "Are you sure?",
      text: "You will be logged out!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, log me out!",
    }).then((result) => {
      if (result.isConfirmed) {
        // Redirect to the logout route
        window.location.href = "{{ route('logout') }}";
      }
    });
  }
</script>

<script>
    function printSection() {
        // Open a new window for printing
        var printWindow = window.open('', '_blank');

        // Get the content of the "print-section" element
        var printContents = document.getElementById("print-section").innerHTML;

        // Write the content to the new window
        printWindow.document.write(printContents);

        // Close the document to ensure proper rendering
        printWindow.document.close();

        // Set the desired width for the "print-image" in the new window
        var printImage = printWindow.document.getElementById("print-image");
        if (printImage) {
            printImage.style.width = "130px";
            printWindow.document.close();
        }

        // Trigger the print dialog for the new window
        printWindow.print();
    }

    $.ajax({
        type: "GET",
        url: "{{ config('app.root_domain') . config('app.user_details_slug') . \Crypt::encryptString(\Auth::id()) }}",
        dataType: 'json',
        success: function(response){
        document.getElementById('fullname').innerHTML = response['first_name'] + " " + response['last_name'];
        // document.getElementById('email').innerHTML = response['email'];
        document.getElementById("profileImage").src = response['profile_photo_url'] ;
        }
    });
</script>

@livewireScripts

@stack('scriptsis')
@stack('scripts_item_history')
@stack('alert_scripts')
