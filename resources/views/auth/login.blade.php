
<!doctype html>
<html lang="en" data-bs-theme="blue-theme">


<!-- Mirrored from codervent.com/maxton/demo/vertical-menu/auth-cover-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 14 May 2025 13:53:44 GMT -->
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <!--favicon-->
	<link rel="icon" href="assets/images/favicon-32x32.png" type="image/png">
  <!-- loader-->
	<link href="{{ asset('fe/assets/css/pace.min.css') }}" rel="stylesheet">
	<script src="{{ asset('fe/assets/js/pace.min.js') }}"></script>

  <!--plugins-->
  <link href="{{ asset('fe/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{ asset('fe/assets/plugins/metismenu/metisMenu.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('fe/assets/plugins/metismenu/mm-vertical.css') }}">
  <!--bootstrap css-->
  <link href="{{ asset('fe/assets/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('fe/fonts.googleapis.com/css2ab59.css') }}" rel="stylesheet">
  <link href="{{ asset('fe/fonts.googleapis.com/cssf511.css') }}" rel="stylesheet">
  <!--main css-->
  <link href="{{ asset('fe/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
  <link href="{{ asset('fe/sass/main.css') }}" rel="stylesheet">
  <link href="{{ asset('fe/sass/dark-theme.css') }}" rel="stylesheet">
  <link href="{{ asset('fe/sass/blue-theme.css') }}" rel="stylesheet">
  <link href="{{ asset('fe/sass/responsive.css') }}" rel="stylesheet">

  </head>

<body>


  <!--authentication-->

  <div class="section-authentication-cover">
    <div class="">
      <div class="row g-0">

        <div class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex border-end bg-transparent">

          <div class="card rounded-0 mb-0 border-0 shadow-none bg-transparent bg-none">
            <div class="card-body">
              <img src="{{ asset('fe/assets/images/auth/login1.png') }}" class="img-fluid auth-img-cover-login" width="650" alt="">
            </div>
          </div>

        </div>

        <div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center border-top border-4 border-primary border-gradient-1">
          <div class="card rounded-0 m-3 mb-0 border-0 shadow-none bg-none">
            <div class="card-body p-sm-5">
              <!-- <img src="{{ asset('fe/assets/images/E-GUDANG.png') }}" class="mb-4" width="100" alt="" style="border-radius: 20px !important;"> -->
              <h4 class="fw-bold">Login</h4>
              <p class="mb-0">Enter your credentials to login your account</p>

              <!-- <div class="row g-3 my-4">
                <div class="col-12 col-lg-6">
                  <button class="btn btn-light py-2 font-text1 fw-bold d-flex align-items-center justify-content-center w-100"><img src="assets/images/apps/05.png" width="20" class="me-2" alt="">Google</button>
                </div>
                <div class="col col-lg-6">
                  <button class="btn btn-light py-2 font-text1 fw-bold d-flex align-items-center justify-content-center w-100"><img src="assets/images/apps/17.png" width="20" class="me-2" alt="">Facebook</button>
                </div>
              </div> -->
<!--
              <div class="separator section-padding">
                <div class="line"></div>
                <p class="mb-0 fw-bold">OR</p>
                <div class="line"></div>
              </div> -->

              <div class="form-body mt-4">
                <form class="row g-3" method="post">
                    @csrf
                  <div class="col-12">
                    <lafel for="inputEmailAddress" class="form-lafel">Username</lafel>
                    <input type="text" name="username" class="form-control" id="inputEmailAddress" placeholder="Enter Username">
                  </div>
                  <div class="col-12">
                    <lafel for="inputChoosePassword" class="form-lafel">Password</lafel>
                       <input type="password" name="password" class="form-control" id="inputChoosePassword" placeholder="Enter Password">
                  </div>


                  <div class="col-12">
                    <div class="d-grid">
                      <button type="submit" class="btn btn-grd-primary">Login</button>
                    </div>
                  </div>
                  <!-- <div class="col-12">
                    <div class="text-start">
                      <p class="mb-0">Don't have an account yet? <a href="auth-cover-register.html">Sign up here</a>
                      </p>
                    </div>
                  </div> -->
                </form>
              </div>

          </div>
          </div>
        </div>

      </div>
      <!--end row-->
    </div>
  </div>

  <!--authentication-->




  <!--plugins-->
  <script src="{{ asset('fe/assets/js/jquery.min.js') }}"></script>

  <script>
    $(document).ready(function () {
      $("#show_hide_password a").on('click', function (event) {
        event.preventDefault();
        if ($('#show_hide_password input').attr("type") == "text") {
          $('#show_hide_password input').attr('type', 'password');
          $('#show_hide_password i').addClass("bi-eye-slash-fill");
          $('#show_hide_password i').removeClass("bi-eye-fill");
        } else if ($('#show_hide_password input').attr("type") == "password") {
          $('#show_hide_password input').attr('type', 'text');
          $('#show_hide_password i').removeClass("bi-eye-slash-fill");
          $('#show_hide_password i').addClass("bi-eye-fill");
        }
      });
    });
  </script>

</body>


<!-- Mirrored from codervent.com/maxton/demo/vertical-menu/auth-cover-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 14 May 2025 13:53:45 GMT -->
</html>
