<?php $this->load->view('layout/header'); ?>

    <h3>Dashboard</h3>

    <div class="row">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5>Total Contacts</h5>
            <p class="fs-4"><?= $count_total ?></p>
          </div>
        </div>
      </div>
    </div>

<?php $this->load->view('layout/footer'); ?>
