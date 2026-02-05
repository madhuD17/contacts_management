<?php $this->load->view('layout/header'); ?>

<h1>404</h1>
<div class="row">
    <div class="col-md">
        <div class="card shadow-sm">
            <div class="card-body">
                <p>Oops! The page you are looking for does not exist.</p>
                <a href="<?= site_url(); ?>">Go to Dashboard</a>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('layout/footer'); ?>
