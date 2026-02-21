<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <ul class="nav flex-column mt-3">
    <li class="nav-item">
      <a class="nav-link <?= ($this->uri->segment(1) == 'dashboard') ? 'active' : ''; ?>" href="<?= base_url('dashboard') ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?= ($this->uri->segment(1) == 'custom_fields') ? 'active' : ''; ?>" href="<?= base_url('custom_fields') ?>">
        <i class="bi bi-folder"></i> Custom Fields
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?= ($this->uri->segment(1) == 'contact_management') ? 'active' : ''; ?>" href="<?= base_url('contact_management') ?>">
        <i class="bi bi-ui-checks"></i> Contacts
      </a>
    </li>
  </ul>
</div>
