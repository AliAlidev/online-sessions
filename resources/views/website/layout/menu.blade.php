<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

<style>

.social-container {
  display: flex;
  gap:4px;
  justify-content: center;
  align-items: center;z-index:25000;
}
ul {
  display: flex;
  gap:4px;
  justify-content: center;
  align-items: center;z-index:25000;
}

ul li {
  list-style: none;
}
ul li a {
  font-size: 36px;
  position: relative;
  color: #fff;
  transition: .5s;
  z-index: 3;
}
</style>
<div class="menu" id="menu">

   <div class="social-container">
     <ul>
        <li><a href="#"><i class="fab fa-telegram-plane"></i></a></li>  
        <li><a href="#"><i class="fab fa-whatsapp icon"></i></a></li>  
        <li><a href="#"><i class="fab fa-instagram icon"></i></a></li>   
        <li><a href="#"><i class="fab fa-facebook icon"></i></a></li> 
        <li><a href="#"><i class="fas fa-envelope"></i></a></li> 
        <li><a href="#"><i class="fab fa-chrome icon"></i></a></li>     
      </ul>
  </div><!-- End Social container -->

  <div style="position:fixed; bottom:10px;">
    <span class="app-brand-logo demo">
      <img src="{{ asset('assets/img/icons/logo-white.svg') }}" width="190px" alt="">
    </span>
  </div>  
    
</div><!-- End Social container -->

