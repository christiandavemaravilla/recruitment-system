<nav class="navbar navbar-light fixed-top" style="padding:10px; background: linear-gradient(to right, #ff8c00, #ffd700); border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);">
  <div class="container-fluid mt-2 mb-2">
    <div class="col-lg-12">
      <div class="col-md-1 float-left" style="display: flex;"> 
      </div>
      <div class="col-md-4 float-left text-white">
        <img src="assets/img/smarthire.png" alt="SmartHire Logo" style="height:35px; width: auto;">
      </div>
      <div class="col-md-2 float-right text-black">
        <a href="ajax.php?action=logout" class="text-white"><?php echo $_SESSION['login_name'] ?> <i class="fa fa-power-off"></i></a>
      </div>
    </div>
  </div>
</nav>