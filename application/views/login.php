<body class="bg-light">
  <h1 align="center" class="logo-daftar-login"><a href="<?=base_url()?>backoffice"><img src="<?=base_url()?>assets/images/logo_bo.png" class="img-fluid" width="170px"></a></h1>
  <div class="container d-flex justify-content-center">
    <div class="box-registrasi-login shadow-sm bg-white">
      <h3 align="center">Masuk</h3>
      <br>
      <?php
      if(isset($_GET['alert'])){
        if($_GET['alert'] == 'wrong'){
          echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
              <strong>Oops!</strong> NIK atau Password Salah.
              <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
          ";
        }
      }
      ?>
      <form class="form-daftar" name="form-login" action="<?=base_url()?>backoffice/login_act" method="post">
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">NIK</label>
          <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="nik">
        </div>
        <div class="mb-3">
          <label for="exampleInputPassword1" class="form-label">Password</label>
          <input type="password" class="form-control" id="exampleInputPassword1" name="password">
        </div>
        <button type="submit" class="btn btn-success">Masuk</button>
      </form>
    </div>
  </div>
</body>