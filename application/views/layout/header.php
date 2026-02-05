<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin CRM</title>
  <link rel="icon" type="image/x-icon" href="<?= $this->config->item('image_path'); ?>favicon.jpeg">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= $this->config->item('css_path'); ?>style.css">

  <link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  


</head>
<body>

<!-- HEADER -->
<nav class="navbar navbar-dark bg-primary fixed-top" style="background-color: #3c8dbc !important;">
  <div class="container-fluid">
      <a href="<?= site_url('dashboard') ?>" class="navbar-brand ms-2">Admin CRM</a>
  </div>
</nav>
<?php $this->load->view('layout/sidebar'); ?>

<div class="content">
  <div class="container-fluid mt-4">
