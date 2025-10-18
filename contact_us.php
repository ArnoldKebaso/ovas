<?php include 'sendemail.php'; ?>
  <body>
    <!--alert messages start-->
    <?php echo $alert; ?>
    <!--alert messages end-->
      <div class="contact-form">
      <form class="contact" action="" method="post" style="background-color: #fff; padding: 25px; max-width: 500px; width: 100%; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); border-radius: 10px; display: flex; flex-direction: column; margin: 0 auto;">
    <input type="text" name="name" class="text-box" placeholder=" Name" required style="width: 100%; padding: 12px 15px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; background-color: #fafafa; transition: 0.3s;">
    <input type="email" name="email" class="text-box" placeholder="Email" required style="width: 100%; padding: 12px 15px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; background-color: #fafafa; transition: 0.3s;">
    <textarea name="message" rows="5" placeholder=" Message" required style="width: 100%; padding: 12px 15px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; background-color: #fafafa; resize: none; transition: 0.3s;"></textarea>
    <input type="submit" name="submit" class="send-btn" value="Send" style="background-color: #007bff; color: #fff; padding: 12px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: 0.3s; align-self: flex-start;">
</form>

      </div>
    </div>
    <!--contact section end-->

    <script type="text/javascript">
    if(window.history.replaceState){
      window.history.replaceState(null, null, window.location.href);
    }
    </script>

  </body>