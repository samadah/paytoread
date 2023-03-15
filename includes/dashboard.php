<?php wp_head(); ?>
<?php
//Put global con outside
 global $wpdb; 
//If form is posted, do
if(isset($_POST['submit']) && !empty($_POST['submit'])) {
    $pid = "1";
  $table = $wpdb->prefix . 'p2rplugins_settings';
    $wpdb->query($wpdb->prepare("UPDATE $table SET site_code='".sanitize_text_field($_POST['site_code'])."' WHERE pid=$pid"));    
}
//Check if the record is present, and get it out
$results = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}p2rplugins_settings WHERE pid = 1" );
if ( null == $results->site_code ) {
  //Do nothing
}else{
  $site_code = $results->site_code;
}
?>
<?php if($site_code == ""){ ?>
<div class="form-v4">
  <div class="page-content">
    <div class="form-v4-content">
      <div class="form-left">
        <h2><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>images/logo.png"></h2>
        <p class="text-1"><span>Welcome:</span> Create paid content on your website using the "Pay to Read" Plugin.</p>
        <p class="text-2"><span>Main Requirement:</span> You will need to have already chosen any of the supported payment gateway and created an account. You will be guided as you proceed.</p>
        <p>Click on the Get Started button to create an account and paste the site code in the box here, then activate.</p>

 
        <div class="form-left-last">
          <a target="_blank" href="https://loopwi.com/dashboard/register"><button type="button"  style="background-color: red;" class="btn btn-success">GET STARTED</button></a>
        </div>
      </div>
      <form id="formABC" class="form-detail" method="POST">
        <h2 style="line-height: normal;">ACTIVATE YOUR ACCOUNT</h2>
         
        <div class="form-row">
          <label for="your_email">Site Code</label>
          <input type="text" style="font-weight: bold; font-size: 24px;" name="site_code" id="site_code" class="input-text" autocomplete="off" value="<?php echo esc_html($site_code); ?>">
        </div> 
         
        <div class="form-row-last"> 
          <input type="submit" name="submit" class="register" value="Submit">
        </div>
        <br>
        Powered by:<br>
        <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>images/logo-light.png">
      </form>
                             
    </div>
  </div>
</div>
<?php }else{ ?>
<br>


    <!-- /.wrapper -->
  <div class="wrapper light-wrapperv">
      <div class="container">
        <div class="row align-items-center">
          <!--/column -->
          <div class="space30 d-none d-md-block d-lg-none"></div>
          <div class="col-lg-12 pr-60 pr-md-15"> 
            <h2 class="section-title">Report</h2>


        <div class="table-responsive">
        <table id="example" class="table table-striped table-bordered" style="width: 100%;"> 
        <thead>
            <tr>
                <th>Date</th> 
                <th>Amount</th>
                <th>Customer</th>
                <th>Email</th>
                <th>URL</th>
                <th>Gateway</th> 
                <th>Reference</th> 
            </tr>
        </thead>
        <tbody>

               <?php
        $response = wp_remote_request( "https://loopwi.com/api/p2r/report?site_code=".$site_code."");
        $body = trim(wp_remote_retrieve_body($response), "\xEF\xBB\xBF"); 
        if ( is_array( $response ) && ! is_wp_error( $response ) ) {
          $body = trim($body, "\xEF\xBB\xBF"); 
          $json = json_decode($body);
          $status = esc_html($json->status); 
          $message = esc_html($json->message); 
          if($status == "error"){
            echo "<h2 style='color:red;'>Error: esc_html($message)</h2>";
          }else{

                  //Loop through jSON response
        
                 foreach( $json->list as $item ) {
       ?>
          
            <tr>
                <td><?php echo esc_html($item->date); ?></td> 
                <td><?php echo number_format(esc_html($item->amount)); ?></td>
                <td><strong><?php echo esc_html($item->fname); ?> <?php echo esc_html($item->lname); ?></strong></td>
                <td><?php echo esc_html($item->email); ?></td>
                <td><?php echo esc_html($item->url); ?></td>
                <td><?php echo esc_html($item->gateway); ?></td> 
                <td><?php echo esc_html($item->ref); ?></td> 
            </tr>

          
              <?php
                    } 
          }
          }
          ?>
            
           </tbody> 

          </table>     
        </div>     

                             
        <small>You can view detailed report at your <a style="color: red; text-decoration: underline;" href="https://loopwi.com">Loopwi Account</a>, and at your payment gateway</small>
            <!-- /.progress-list -->
          </div>
          <!--/column -->
        </div>
        <!--/.row -->
        
      </div>
      <!-- /.container -->
    </div>
 

    <br>
    <br>
<div>
   <strong>Submitted Site Code: <em><?php echo esc_html($site_code); ?></em></strong>. <a style="text-decoration: underline;"  href="<?php echo esc_url( admin_url( '/admin.php?page=loopwi_p2r_settings' ) ); ?>">Change This?</a>

</div>
<?php } ?>