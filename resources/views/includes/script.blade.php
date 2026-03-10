 <!-- General JS Scripts -->
 <script src="{{ asset('be/dist/assets/modules/jquery.min.js') }}"></script>
 <script src="{{ asset('be/dist/assets/modules/popper.js') }}"></script>
 <script src="{{ asset('be/dist/assets/modules/tooltip.js') }}"></script>
 <script src="{{ asset('be/dist/assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
 <script src="{{ asset('be/dist/assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
 <script src="{{ asset('be/dist/assets/modules/moment.min.js') }}"></script>
 <script src="{{ asset('be/dist/assets/js/stisla.js') }}"></script>

 <!-- JS Libraies -->
 <script src="{{ asset('be/dist/assets/modules/simple-weather/jquery.simpleWeather.min.js') }}"></script>
 <script src="{{ asset('be/dist/assets/modules/chart.min.js') }}"></script>
 <script src="{{ asset('be/dist/assets/modules/jqvmap/dist/jquery.vmap.min.js') }}"></script>
 <script src="{{ asset('be/dist/assets/modules/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
 <script src="{{ asset('be/dist/assets/modules/summernote/summernote-bs4.js') }}"></script>
 <script src="{{ asset('be/dist/assets/modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
 <script src="{{ asset('be/sweetalert.js') }}"></script>
 <!-- <script src="{{ asset('be/dist/assets/modules/datatables/datatables.min.js') }}"></script>
 <script src="{{ asset('be/dist/assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
 <script src="{{ asset('be/dist/assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js') }}"></script>
  -->
 <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 <!-- Page Specific JS File -->
 <script src="{{ asset('be/dist/assets/js/page/index-0.js') }}"></script>

 <!-- Template JS File -->
 <script src="{{ asset('be/dist/assets/js/scripts.js') }}"></script>
 <script src="{{ asset('be/dist/assets/js/custom.js') }}"></script>

 @stack('script')
