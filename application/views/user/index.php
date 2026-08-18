<body class="horizontal-layout horizontal-menu 1-column  bg-full-screen-image menu-expanded blank-page blank-page"
      data-open="hover" data-menu="horizontal-menu" data-col="1-column">
<!-- ////////////////////////////////////////////////////////////////////////////-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section class="flexbox-container">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-md-6 col-sm-10 box-shadow-2 p-1">
                        <div class="card border-grey border-lighten-3 px-1 py-1 m-0">
                            <div class="card-header border-0">
                                <div class="card-title text-center">
                                    <h3>Thok ki Dukan</h3>
                                </div>
                                <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                                    <span>Login Panel</span></h6>
                            </div>
                            <div class="card-content">


                              <!--  <div class="card-body">
                                    <?php
                                    $attributes = array('class' => 'form-horizontal form-simple', 'id' => 'login_form');
                                    echo form_open('user/checklogin', $attributes);
                                    ?>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" id="user-name" name="username"
                                               placeholder="Your Mobile Number" required>
                                        <div class="form-control-position">
                                            <i class="ft-user"></i>
                                        </div>
                                    </fieldset>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="password" class="form-control" id="user-password" name="password"
                                               placeholder="<?php echo $this->lang->line('Your Password') ?>" required>
                                        <div class="form-control-position">
                                            <i class="fa fa-key"></i>
                                        </div>
                                    </fieldset>
                                    <?php if ($response) {
                                        echo '<div id="notify" class="alert alert-danger" >
                            <a href="#" class="close" data-dismiss="alert">&times;</a> <div class="message">' . $response . '</div>
                        </div>';
                                    } ?>

                                    <?php if ($this->aauth->get_login_attempts() > 1 && $captcha_on) {
                                        echo '<script src="https://www.google.com/recaptcha/api.js"></script>
									<fieldset class="form-group position-relative has-icon-left">
                                      <div class="g-recaptcha" data-sitekey="' . $captcha . '"></div>
                                    </fieldset>';
                                    } ?>
                                    <div class="form-group row">
                                        <div class="col-md-6 col-12 text-center text-sm-left">
                                            <fieldset>
                                                <input type="checkbox" id="remember-me" class="chk-remember"
                                                       name="remember_me">
                                                <label for="remember-me">  <?php echo $this->lang->line('remember_me') ?></label>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-6 col-12 float-sm-left text-center text-sm-right"><a
                                                    href="<?php echo base_url('user/forgot'); ?>"
                                                    class="card-link"><?php echo $this->lang->line('forgot_password') ?>
                                                ?</a></div>
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary btn-block"><i
                                                class="ft-unlock"></i> <?php echo $this->lang->line('login') ?></button>
                                    </form>
                                </div> -->
					<button type="button" class="btn btn-outline-primary btn-block" id="loginbypassword">
    <i class="ft-unlock"></i> Login by Email & Password
</button>
<button type="button" class="btn btn-outline-primary btn-block" id="loginbyotpbtn">
    <i class="ft-unlock"></i> Login by Mobile OTP
</button>

<!-- Password login form (hidden initially) -->
<div class="card-body" style="display:none;" id="loginbypassworddiv">
    <span class="login-back-btn" style="position: absolute; right: 15px; top: 15px; cursor: pointer; font-size: 24px; font-weight: bold; color: #999; line-height: 1; z-index: 10;" title="Go Back">&times;</span>
    <?php
    $attributes = array('class' => 'form-horizontal form-simple', 'id' => 'login_form');
    echo form_open('user/checklogin', $attributes);
    ?>
    <fieldset class="form-group position-relative has-icon-left">
        <input type="text" class="form-control" id="user-name" name="username" placeholder="Your Email Address" required>
        <div class="form-control-position">
            <i class="ft-user"></i>
        </div>
    </fieldset>
    <fieldset class="form-group position-relative has-icon-left">
        <input type="password" class="form-control" id="user-password" name="password" placeholder="Your Password" required>
        <div class="form-control-position">
            <i class="fa fa-key"></i>
        </div>
    </fieldset>

    <div class="form-group row">
        <div class="col-md-6 col-12">
            <input type="checkbox" id="remember-me" class="chk-remember" name="remember_me">
            <label for="remember-me">Remember Me</label>
        </div>
        <div class="col-md-6 col-12 text-right">
            <a href="<?php echo base_url('user/forgot'); ?>" class="card-link">Forgot Password?</a>
        </div>
    </div>
    <button type="submit" class="btn btn-outline-primary btn-block" id="loginBtn">
        <i class="ft-unlock"></i> Login
    </button>
    </form>
</div>

<div class="card-body" id="otpMobileForm" style="display: none;">
    <span class="login-back-btn" style="position: absolute; right: 15px; top: 15px; cursor: pointer; font-size: 24px; font-weight: bold; color: #999; line-height: 1; z-index: 10;" title="Go Back">&times;</span>
    <fieldset class="form-group position-relative has-icon-left">
        <input type="text" class="form-control" id="mobile-number" name="mobile_number" placeholder="Enter Mobile Number" required>
        <div class="form-control-position">
            <i class="ft-phone"></i>
        </div>
    </fieldset>
    <button type="button" class="btn btn-outline-primary btn-block" id="generateOtpBtn">
        Generate OTP
    </button>
	<div id="loader" style="display:none; position:fixed; left:50%; top:50%; transform:translate(-50%, -50%); z-index:9999;">
    <img src="path_to_loader.gif" alt="Loading..." />
</div>
</div>

<div class="card-body" id="otpForm" style="display: none;">
    <span class="login-back-btn" style="position: absolute; right: 15px; top: 15px; cursor: pointer; font-size: 24px; font-weight: bold; color: #999; line-height: 1; z-index: 10;" title="Go Back">&times;</span>
    <fieldset class="form-group position-relative has-icon-left">
        <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter OTP" required>
        <div class="form-control-position">
            <i class="fa fa-key"></i>
        </div>
    </fieldset>
    <button type="button" class="btn btn-outline-primary btn-block" id="verifyOtpBtn">
        Verify OTP
    </button>
    <button type="button" class="btn btn-outline-secondary btn-block" id="resendOtpBtn">
        Resend OTP
    </button>
</div>

                        
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>
<!-- ////////////////////////////////////////////////////////////////////////////-->

<script src="<?= assets_url(); ?>app-assets/vendors/js/vendors.min.js"></script>
<script type="text/javascript" src="<?= assets_url(); ?>app-assets/vendors/js/ui/jquery.sticky.js"></script>
<script type="text/javascript" src="<?= assets_url(); ?>app-assets/vendors/js/charts/jquery.sparkline.min.js"></script>
<script src="<?= assets_url(); ?>app-assets/vendors/js/forms/validation/jqBootstrapValidation.js"></script>
<script src="<?= assets_url(); ?>app-assets/vendors/js/forms/icheck/icheck.min.js"></script>
<script src="<?= assets_url(); ?>app-assets/js/core/app-menu.js"></script>
<script src="<?= assets_url(); ?>app-assets/js/core/app.js"></script>
<script type="text/javascript" src="<?= assets_url(); ?>app-assets/js/scripts/ui/breadcrumbs-with-stats.js"></script>
<!--<script src="<?= assets_url(); ?>app-assets/js/scripts/forms/form-login-register.js"></script> -->
<script>

$(document).ready(function() {
    // Back to options handler (cross button)
    $('.login-back-btn').on('click', function() {
        $('#loginbypassworddiv').hide();
        $('#otpMobileForm').hide();
        $('#otpForm').hide();
        $('#loginbypassword').show();
        $('#loginbyotpbtn').show();
    });

    // Show password login form
    $('#loginbypassword').on('click', function() {
        $('#loginbypassword').hide();
        $('#loginbyotpbtn').hide();
        $('#loginbypassworddiv').show();
    });

    // Show OTP mobile input form
    $('#loginbyotpbtn').on('click', function() {
        $('#loginbypassword').hide();
        $('#loginbyotpbtn').hide();
        $('#otpMobileForm').show();
    });

    // Generate OTP after entering mobile number
    $('#generateOtpBtn').on('click', function() {
        var mobileNumber = $.trim($('#mobile-number').val()); // Get mobile number

        if (mobileNumber === "") {
            alert("Please enter your mobile number.");
            return;
        }

        $('#loader').show();

        $.ajax({
            url: "<?php echo base_url('user/generate-otp'); ?>",
            type: "POST",
            data: { mobile_number: mobileNumber },
            dataType: "json",
            success: function(response) {
                $('#loader').hide();
                if (response.success) {
                    alert("OTP sent to your mobile number.");
                    $('#mobile-number').val(mobileNumber);
                    $('#otpMobileForm').hide();
                    $('#otpForm').show();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                $('#loader').hide();
                alert("An error occurred while sending OTP. Please try again.");
            }
        });
    });

    // Handle OTP verification
    $('#verifyOtpBtn').on('click', function() {
        var otp = $('#otp').val(); // Get the entered OTP

        if (otp === "") {
            alert("Please enter the OTP.");
            return;
        }
        var mobileNumber = $.trim($('#mobile-number').val());
        $.ajax({
            url: "<?php echo base_url('user/verify-otp'); ?>",
            type: "POST",
            data: { otp: otp, mobile_number: mobileNumber },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    window.location.href = "<?php echo base_url('dashboard'); ?>";
                } else {
                    alert(response.message || "Invalid OTP. Please try again.");
                }
            },
            error: function() {
                alert("Error verifying OTP. Please try again.");
            }
        });
    });

    // Resend OTP
    $('#resendOtpBtn').on('click', function() {
        var mobileNumber = $.trim($('#mobile-number').val());

        if (mobileNumber === "") {
            alert("Please enter your mobile number.");
            return;
        }

        $.ajax({
            url: "<?php echo base_url('user/resend-otp'); ?>",
            type: "POST",
            data: { mobile_number: mobileNumber },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    alert("OTP resent to your mobile number.");
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert("An error occurred while resending OTP. Please try again.");
            }
        });
    });
});

</script>
