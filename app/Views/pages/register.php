<!-- pages: register -->
<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>
<main class="register">
    <section class="hero is-fullheight">
        <div class="columns is-vcentered">
            <!-- Register Form Column -->
            <div class="column is-5 register__container">
                <div class="hero is-fullheight">
                    <div class="hero-body">
                        <div class="container">
                            <div class="columns is-centered">
                                <div class="column is-10-tablet is-9-desktop is-8-widescreen">
                                    <form action="" class="box register-form" id="register-form">
                                        <figure class="image is-128x128 has-text-centered register__logo">
                                            <img src="<?= base_url('resources/img/logo/logo.png') ?>" alt="Logo" class="register__logo">
                                        </figure>
                                        <div class="field">
                                            <label class="label" for="register-email-input">Email</label>
                                            <div class="control has-icons-left">
                                                <input id="register-email-input" type="email" placeholder="e.g. bobsmith@gmail.com" class="input" required>
                                                <span class="icon is-small is-left">
                                                    <i class="fa fa-envelope"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label" for="register-password-input">Password</label>
                                            <div class="control has-icons-left">
                                                <input id="register-password-input" type="password" placeholder="*******" class="input" required>
                                                <span class="icon is-small is-left">
                                                    <i class="fa fa-lock"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label" for="register-password-confirm-input">Confirm Password</label>
                                            <div class="control has-icons-left">
                                                <input id="register-password-confirm-input" type="password" placeholder="*******" class="input" required>
                                                <span class="icon is-small is-left">
                                                    <i class="fa fa-lock"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <!-- Personal Information Fields -->
                                        <div class="field">
                                            <label class="label" for="register-firstname-input">First Name</label>
                                            <div class="control">
                                                <input id="register-firstname-input" type="text" placeholder="First Name" class="input" name="first_name" required>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label" for="register-lastname-input">Last Name</label>
                                            <div class="control">
                                                <input id="register-lastname-input" type="text" placeholder="Last Name" class="input" name="last_name" required>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label" for="register-phone-input">Phone</label>
                                            <div class="control">
                                                <input id="register-phone-input" type="tel" placeholder="Phone Number" class="input" name="phone">
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label" for="register-address-input">Address</label>
                                            <div class="control">
                                                <input id="register-address-input" type="text" placeholder="Address" class="input" name="address">
                                            </div>
                                        </div>
                                        <!-- End Personal Information Fields -->
                                        <div class="field mt-6">
                                            <button type="submit" class="button is-success" id="register-button">
                                                Register
                                            </button>
                                        </div>
                                        <div class="field">
                                            <p id="register-error-message" class="help is-danger is-size-6-mobile is-size-6-tablet is-size-6-desktop is-size-6-widescreen"></p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Register Form Column -->
                <!-- Background Image Column -->
            <div class="column ___set-background is-hidden-touch">
                <div class="background-image-login"></div>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>