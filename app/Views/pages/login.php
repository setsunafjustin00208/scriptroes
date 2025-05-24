<!-- pages: login -->
<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>
<main class="login">
    <section class="hero is-fullheight">
        <div class="columns is-vcentered">
            <!-- Background Image Column -->
            <div class="column ___set-background is-hidden-touch">
                <div class="background-image-login"></div>
            </div>
            <!-- Login Form Column -->
            <div class="column is-5 login__container">
                <div class="hero is-fullheight">
                    <div class="hero-body">
                        <div class="container">
                            <div class="columns is-centered">
                                <div class="column is-10-tablet is-9-desktop is-8-widescreen">
                                    <form action="" class="box" id="login-form">
                                        <figure class="image is-128x128 has-text-centered login__logo">
                                            <img src="<?= base_url('resources/img/logo/logo.png') ?>" alt="Logo" class="login__logo">
                                        </figure>
                                        <div class="field">
                                            <label class="label" for="email-input">Email</label>
                                            <div class="control has-icons-left">
                                                <input id="email-input" type="email" placeholder="e.g. bobsmith@gmail.com" class="input" required>
                                                <span class="icon is-small is-left">
                                                    <i class="fa fa-envelope"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label" for="password-input">Password</label>
                                            <div class="control has-icons-left">
                                                <input id="password-input" type="password" placeholder="*******" class="input" required>
                                                <span class="icon is-small is-left">
                                                    <i class="fa fa-lock"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="checkbox">
                                                <input type="checkbox" id="remember_me"> Remember me
                                            </label>
                                        </div>
                                        <div class="field">
                                            <button type="submit" class="button is-success" id="login-button">
                                                Login
                                            </button>
                                        </div>
                                        <div class="field">
                                            <p id="error-message" class="help is-danger is-size-6-mobile is-size-6-tablet is-size-6-desktop is-size-6-widescreen"></p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Login Form Column -->
        </div>
    </section>
<?= $this->endSection() ?>Hey, Cortana. 