<?php

    session_start();

    function getRealIp() {
       if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
         $ip=$_SERVER['HTTP_CLIENT_IP'];
       } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
         $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
       } else {
         $ip=$_SERVER['REMOTE_ADDR'];
       }
       return $ip;
    }

    function writeLog($where) {

    	$ip = getRealIp(); // Get the IP from superglobal
    	$host = gethostbyaddr($ip);    // Try to locate the host of the attack
    	$date = date("d M Y");

    	// create a logging message with php heredoc syntax
    	$logging = <<<LOG
    		\n
    		<< Start of Message >>
    		There was a hacking attempt on your form. \n
    		Date of Attack: {$date}
    		IP-Adress: {$ip} \n
    		Host of Attacker: {$host}
    		Point of Attack: {$where}
    		<< End of Message >>
LOG;
// Awkward but LOG must be flush left

            // open log file
    		if($handle = fopen('hacklog.log', 'a')) {

    			fputs($handle, $logging);  // write the Data to file
    			fclose($handle);           // close the file

    		} else {  // if first method is not working, for example because of wrong file permissions, email the data

    			$to = 'customstoneworkpk@gmail.com';
            	$subject = 'HACK ATTEMPT';
            	$header = 'From: customstoneworkpk@gmail.com';
            	if (mail($to, $subject, $logging, $header)) {
            		echo "Sent notice to admin.";
            	}

    		}
    }

    function verifyFormToken($form) {

        // check if a session is started and a token is transmitted, if not return an error
    	if(!isset($_SESSION[$form.'_token'])) {
    		return false;
        }
      error_log("Hey");
    	// check if the form is sent with token in it
    	if(!isset($_POST['token'])) {
    		return false;
        }

    	// compare the tokens against each other if they are still the same
    	if (hash_equals($_SESSION[$form.'_token'], $_POST['token'])) {
    		return true;
        }

    	return false;
    }

    function generateFormToken($form) {

        // generate a token from an unique value, you can also use salt-values, other crypting methods...
    	$token = bin2hex(random_bytes(32));
    	// Write the generated token to the session variable to check it against the hidden field when the form is sent
    	$_SESSION[$form.'_token'] = $token;
    	return $token;
    }

    // VERIFY LEGITIMACY OF TOKEN
    if (verifyFormToken('quoteform')) {

        // CHECK TO SEE IF THIS IS A MAIL POST
        if (isset($_POST['email'])) {

            // Building a whitelist array with keys which will send through the form, no others would be accepted later on
            $whitelist = array('token','name','email','phone', 'comment','list');

            // Building an array with the $_POST-superglobal
            foreach ($_POST as $key=>$item) {

                    // Check if the value $key (fieldname from $_POST) can be found in the whitelisting array, if not, die with a short message to the hacker
            		if (!in_array($key, $whitelist)) {

            			writeLog('Unknown form fields');
            			die("Hack-Attempt detected. Please use only the fields in the form");

            		}
            }

          	$name =$_POST["name"];
          	$from =$_POST["email"];
          	$phone=$_POST["phone"];
          	$comment=$_POST["comment"];
            $list = $_POST["list"];

          	// Email Receiver Address
          	$receiver="customstoneworkpk@gmail.com";
          	$subject="Quote requested by ".strip_tags($name);

          	$message = "
          	<html>
          	<head>
          	<title>HTML email</title>
          	</head>
          	<body>
          	<table width='50%' border='0' align='center' cellpadding='0' cellspacing='0'>
          	<tr>
          	<td colspan='2' align='center' valign='top'><img style=' width: 150px; height: auto; margin-top: 15px; ' src='http://www.customstoneworkpk.com/images/csw-logo-black-email.png' ></td>
          	</tr>
          	<tr>
          	<td width='50%' align='right'>&nbsp;</td>
          	<td align='left'>&nbsp;</td>
          	</tr>
          	<tr>
          	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Name:</td>
          	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>".strip_tags($name)."</td>
          	</tr>
          	<tr>
          	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Email:</td>
          	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>".strip_tags($from)."</td>
          	</tr>
          		<tr>
          	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Phone:</td>
          	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>".strip_tags($phone)."</td>
          	</tr>
          	<tr>
          	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; border-bottom:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Message:</td>
          	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; border-bottom:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>".strip_tags(nl2br($comment))."</td>
          	</tr>
            <tr>
          	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; border-bottom:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Quote Requested For:</td>
          	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; border-bottom:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>".strip_tags(nl2br($list))."</td>
          	</tr>
          	</table>
          	</body>
          	</html>
          	";

            //  MAKE SURE THE "FROM" EMAIL ADDRESS DOESN'T HAVE ANY NASTY STUFF IN IT

            $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i";
                  if (preg_match($pattern, trim(strip_tags($_POST['email'])))) {
                      $cleanedFrom = trim(strip_tags($_POST['email']));
                  } else {
                      return "The email address you entered was invalid. Please try again!";
                  }

            //   CHANGE THE BELOW VARIABLES TO YOUR NEEDS

            $to = 'customstoneworkpk@gmail.com';

            $headers = "From: " . $cleanedFrom . "\r\n";
            $headers .= "Reply-To: ". strip_tags($_POST['email']) . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            if (mail($to, $subject, $message, $headers)) {
              //thank you redirect
               header('Location: quote-list-submitted.html');
            } else {
              echo 'There was a problem sending the email.';
            }

            // DON'T BOTHER CONTINUING TO THE HTML...
            die();

          }
      }

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
  <!-- title -->
  <title>The Rockyard | Quote List</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1" />
  <meta name="author" content="Custom Stone Work">
  <!-- description -->
  <meta name="description" content="Custom Stone Work and Rockyard provides masonry services and masonry supplies to Possum Kingdom Lake, Graford, Graham, Bryson, Breckenridge, Jacksboro, Mineral Wells, and beyond.">
  <!-- keywords -->
  <meta name="keywords" content="stone, masonry, graford, patio, gravel, stonemason, custom, contractor, rock, retaining wall, siding, outdoor kitchen">
  <!-- Open Graphs properties -->
  <meta property="og:url" content="https://customstoneworkpk.com/" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Custom Stone Work &amp; Rockyard" />
  <meta property="og:image" content="https://customstoneworkpk.com/images/1200x697_og-image-01.JPG" />
  <meta property="og:description" content="Expert Masons. Full Supply Yard. Equipment Services." />
  <!-- favicon -->
  <link rel="shortcut icon" href="images/favicon.png">
  <link rel="apple-touch-icon" href="images/apple-touch-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
  <!-- style -->
  <link rel="stylesheet" href="css/style.min.css" />
  <!--[if IE]>
            <script src="js/html5shiv.js"></script>
        <![endif]-->
</head>
<?php
  // generate a new token for the $_SESSION superglobal and put them in a hidden field
  $newToken = generateFormToken('quoteform');
?>
<body>
  <header>
    <!-- start navigation -->
    <nav class="navbar navbar-default bootsnav navbar-top header-dark bg-deep-pink nav-box-width white-link">
      <div class="container-fluid nav-header-container">
        <div class="row">
          <!-- start logo -->
          <div class="col-md-2 col-xs-5">
            <a href="index.html" title="Custom Stone Work" class="logo"><img src="images/csw-logo-white.png" class="logo-dark" alt="Custom Stone Work"><img src="images/csw-logo-white.png" alt="Custom Stone Work" class="logo-light default"></a>
          </div>
          <!-- end logo -->
          <div class="col-md-7 col-xs-2 width-auto pull-right accordion-menu">
            <button type="button" class="navbar-toggle collapsed pull-right" data-toggle="collapse" data-target="#navbar-collapse-toggle-1">
              <span class="sr-only">toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <div class="navbar-collapse collapse pull-right" id="navbar-collapse-toggle-1">
              <ul id="accordion" class="nav navbar-nav navbar-left no-margin alt-font text-normal" data-in="fadeIn" data-out="fadeOut">
                <!-- start menu item -->
                <li class="dropdown megamenu-fw">
                  <a href="index.html">Home</a>
                <li class="">
                  <a href="stone-mason.html">Stone Work</a>
                </li>
                <li class="">
                  <a href="customlandservices.html">Land Services</a>
                </li>
                <li class="dropdown megamenu-fw">
                  <a href="portfolio.html">Portfolio</a>
                </li>
                <li class=""><a href="the-rockyard-on-possum-kingdom.html" title="Rockyard">Rockyard</a>
                </li>
                <li class="dropdown megamenu-fw">
                  <a href="rockyard-products.html">Products</a><i class="fas fa-angle-down dropdown-toggle" data-toggle="dropdown" aria-hidden="true"></i>
                  <!-- start sub menu -->
                  <div class="menu-back-div dropdown-menu megamenu-content mega-menu collapse mega-menu-full">
                    <ul class="equalize sm-equalize-auto">
                      <!-- start sub menu column  -->
                      <li class="mega-menu-column col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <!-- start sub menu item  -->
                        <ul>
                          <li class="dropdown-header">Natural Stone</li>
                          <li><a href="boulders.html">Boulders</a></li>
                          <li><a href="builders-stone.html">Builders Stone</a></li>
                          <li><a href="chopped-stone.html">Chopped Stone</a></li>
                          <li><a href="flagstone.html">Flagstone</a></li>
                          <li><a href="gravel.html">Gravel and River Rock</a></li>
                          <li><a href="sawn-and-honed.html">Sawn and Honed Stone</a></li>
                          <li><a href="stone-slabs.html">Stone Slabs</a></li>
                          <li><a href="equipment-services.html">Equipment Services</a></li>
                        </ul>
                        <!-- end sub menu item  -->
                      </li>
                      <!-- end sub menu column -->
                      <!-- start sub menu column -->
                      <li class="mega-menu-column col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <!-- start sub menu item  -->
                        <ul>
                          <li class="dropdown-header">Masonry Supplies</li>
                          <li><a href="cement.html">Cement</a></li>
                          <li><a href="chemicals.html">Masonry Chemicals</a></li>
                          <li><a href="concrete-blocks.html">Concrete Blocks</a></li>
                          <li><a href="dirt.html">Dirt, Sand, and Mulch</a></li>
                          <li><a href="steel.html">Steel Products</a></li>
                          <li><a href="tools.html">Tools</a></li>
                          <li><a href="weed-barriers.html">Weed Barriers</a></li>
                          <li><a href="equipment-services.html">Equipment Services</a></li>
                        </ul>
                        <!-- end sub menu item  -->
                      </li>
                      <!-- end sub menu column  -->

                      <!-- start sub menu column  -->
                      <li class="mega-menu-column col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <!-- start sub menu item  -->
                        <ul>
                          <li class="text-center bg-deep-pink">
                            <div class="alt-font font-large text-white center-col padding-five-all">Haven't seen what you're looking for yet?<br />Let us source materials for your project</div>
                          </li>
                          <li>
                            <a href="contact.php#contact" class="text-center bg-black">Send an Inquiry&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></a>
                          </li>
                        </ul>
                        <!-- end sub menu item  -->
                      </li>
                      <!-- end sub menu column  -->
                    </ul>
                    <!-- end sub menu -->
                  </div>
                </li>
                <li class="">
                  <a href="contact.php">Contact</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>
    <!-- end navigation -->
  </header>
  <!-- end header -->
  <!-- start page title section -->
  <section id="" class="wow fadeIn bg-extra-dark-gray padding-one-half-tb page-title-small top-space">
    <div class="container">
      <div class="row equalize">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 display-table">
          <div class="display-table-cell vertical-align-middle text-left xs-text-center">
            <!-- start page title -->
            <h1 class="alt-font text-white font-weight-600 no-margin-bottom xs-padding-15px-tb text-uppercase">QUOTE FORM</h1>
            <!-- end page title -->
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end page title section -->
  <!-- start page title section -->
  <section id="natural-stone" class="wow fadeIn bg-medium-light-gray padding-one-half-tb page-title-small">
    <div class="container">
      <div class="row equalize">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 display-table">
          <div class="display-table-cell vertical-align-middle text-left xs-text-center">
            <!-- start page title -->
            <h1 class="alt-font text-extra-dark-gray font-weight-400 no-margin-bottom text-uppercase">Natural Stone</h1>
            <!-- end page title -->
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 display-table text-right xs-text-left xs-margin-15px-tb">
          <div class="display-table-cell vertical-align-middle breadcrumb text-small alt-font">
            <!-- breadcrumb -->
            <ul class="xs-text-center">
              <li><a href="boulders.html" class="text-medium-gray">Boulders</a></li>
              <li><a href="builders-stone.html" class="text-medium-gray">Builders Stone</a></li>
              <li><a href="chopped-stone.html" class="text-medium-gray">Chopped Stone</a></li>
              <li><a href="flagstone.html" class="text-medium-gray">Flagstone</a></li>
              <li><a href="gravel.html" class="text-medium-gray">Gravel &amp; River Rock</a></li>
              <li><a href="sawn-and-honed.html" class="text-medium-gray">Sawn &amp; Honed Stone</a></li>
              <li><a href="stone-slabs.html" class="text-medium-gray">Stone Slabs</a></li>
              <li><a href="equipment-services.html" class="text-medium-gray">Equipment Services</a></li>
            </ul>
            <!-- end breadcrumb -->
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end page title section -->
  <!-- start page title section -->
  <section id="masonry-supplies" class="wow fadeIn bg-light-gray padding-one-half-tb page-title-small">
    <div class="container">
      <div class="row equalize">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 display-table">
          <div class="display-table-cell vertical-align-middle text-left xs-text-center">
            <!-- start page title -->
            <h1 class="alt-font text-extra-dark-gray font-weight-400 no-margin-bottom text-uppercase">Masonry Supplies</h1>
            <!-- end page title -->
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 display-table text-right xs-text-left xs-margin-15px-tb">
          <div class="display-table-cell vertical-align-middle breadcrumb text-small alt-font">
            <!-- breadcrumb -->
            <ul class="xs-text-center">
              <li><a href="cement.html" class="text-medium-gray">Cement</a></li>
              <li><a href="chemicals.html" class="text-medium-gray">Masonry Chemicals</a></li>
              <li><a href="concrete-blocks.html" class="text-medium-gray">Concrete Blocks</a></li>
              <li><a href="dirt.html" class="text-medium-gray">Dirt, Sand, &amp; Mulch</a></li>
              <li><a href="steel.html" class="text-medium-gray">Steel Products</a></li>
              <li><a href="tools.html" class="text-medium-gray">Tools &amp; Hardware</a></li>
              <li><a href="weed-barriers.html" class="text-medium-gray">Weed Barriers</a></li>
              <li><a href="equipment-services.html" class="text-medium-gray">Equipment Services</a></li>
            </ul>
            <!-- end breadcrumb -->
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end page title section -->
  <!-- start page title section -->
  <section id="" class="wow fadeIn bg-extra-dark-gray padding-one-half-tb page-title-small">
    <div class="container">
      <div class="row equalize">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 display-table">
          <div class="display-table-cell vertical-align-middle text-left xs-text-center">
            <!-- start page title -->
            <h1 class="alt-font text-white font-weight-600 no-margin-bottom xs-padding-15px-tb text-uppercase">QUOTE FORM</h1>
            <!-- end page title -->
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end page title section -->
  <!-- start quote list section -->
  <section class="wow fadeIn bg-white padding-one-half-tb xs-no-padding-tb">
    <div class="container-fluid">
      <div class="row margin-auto padding-five-lr xs-no-padding-lr">
        <div class="col-md-12 col-sm-12 col-xs-12 xs-no-padding-lr">
          <div id ="quote-list-container" class="position-relative overflow-hidden text-center">
            <div id ="drop-down-row" class="quote-list-container-row border-1px-solid padding-10px-tb col-md-12">
              <button class="quote-list-line quote-list-quantity-minus ms-grid-minus"><i class="fa fa-minus"></i></button>
              <div class="quote-list-line quote-list-quantity-counter ms-grid-counter">
                <span class="item-counter">1</span>
              </div>
              <button class="quote-list-line quote-list-quantity-plus ms-grid-plus"><i class="fa fa-plus"></i></button>
              <button class="quote-list-line quote-list-add ms-grid-add"><i class="fa fa-check"></i></button>
              <div class="quote-list-line quote-list-item-name ms-grid-name">
                <span>
                  <select id="dropDownSelect">
                    <option value="Select">Select an item to add...</option>
                  </select>
                </span>
              </div>
              <div class="quote-list-line quote-list-item-description ms-grid-desc" style="text-align:left;"></div>
            </div>
          </div>
          <form id="quote-list-form" name="quoteform" action="quote-list.php" method="post">
            <input type="hidden" name="token" value="<?php echo $newToken; ?>" />
            <div class="col-md-12 padding-one-half-tb">
              <a class="update-list-quantities btn btn-small btn-rounded btn-transparent-black margin-10px-bottom margin-10px-top margin-30px-right xs-width-100">Update list quantities</a>
            </div>
            <div class="col-md-6">
                <input type="text" required name="name" id="name" placeholder="Name *" class="bg-transparent border-color-medium-dark-gray medium-input">
            </div>
            <div class="col-md-6">
                <input type="text" name="phone" id="phone" placeholder="Phone" class="bg-transparent border-color-medium-dark-gray medium-input">
            </div>
            <div class="col-md-6">
                <input type="text" required name="email" id="email" placeholder="E-mail *" class="bg-transparent border-color-medium-dark-gray medium-input">
            </div>
            <div class="col-md-12">
                <textarea name="comment" id="comment" placeholder="Fill us in on any additional details here:" rows="4" class="bg-transparent border-color-medium-dark-gray medium-textarea"></textarea>
            </div>
            <div class="col-md-12">
                <textarea name="list" id="list" placeholder="the list gets populated here on submit" class="display-none"></textarea>
            </div>
            <div class="col-md-12">
                <input onclick="submitForQuote()" type="submit" value="Submit for quote" class="btn btn-small btn-rounded btn-deep-pink margin-10px-bottom margin-10px-top xs-width-100 xs-margin-40px-bottom"></input>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <!-- end stone/supplies banner section -->

  <!-- start call to action -->
  <section class="wow fadeIn padding-60px-tb sm-padding-40px-tb bg-deep-pink">
    <div class="container">
      <div class="row">
        <div class="col-md-12 col-sm-12 center-col position-relative text-center">
          <span class="text-extra-large text-white alt-font display-inline-block margin-5px-top margin-30px-right md-no-margin-right xs-no-margin-top md-margin-15px-bottom xs-width-100">Looking for something else?</span>
          <a href="tel:19407793700" class="btn btn-white btn-medium btn-rounded vertical-align-top max-width-75 center-col display-none sm-display-block">Tell us About Your Project<i class="fas fa-phone"></i></a>
          <a href="contact.php#contact" class="btn btn-white btn-medium btn-rounded vertical-align-top xs-no-margin-right sm-display-none">Tell us About Your Project<i class="fas fa-envelope"></i></a>
        </div>
      </div>
    </div>
  </section>
  <!-- end call to action -->

  <!-- start footer -->
  <footer class="footer-standard-dark bg-extra-dark-gray">
    <div class="footer-widget-area padding-five-tb xs-padding-30px-tb">
      <div class="container">
        <div class="row equalize xs-equalize-auto">
          <div class="col-md-4 col-sm-6 col-xs-12 widget border-right border-color-medium-dark-gray sm-no-border-right sm-margin-30px-bottom xs-text-center">
            <!-- start logo -->
            <a href="index.html" class="margin-20px-bottom display-inline-block"><img class="footer-logo" src="images/csw-logo-white.png" alt="Custom Stone Work"></a>
            <!-- end logo -->
            <p class="text-small width-95 xs-width-100">Custom Stone Work and Rockyard has provided high quality masonry services and masonry supplies to the greater Possum Kingdom Lake area for over 25 years. We serve Graford, Graham, Bryson,
              Breckenridge, Jacksboro, Mineral Wells, and beyond.</p>
            <!-- start social media -->
            <div class="social-icon-style-8 display-inline-block vertical-align-middle">
              <ul class="small-icon no-margin-bottom">
                <li><a class="facebook text-white" href="https://www.facebook.com/customstoneworkandrockyard" target="_blank"><i class="fab fa-facebook-f" aria-hidden="true"></i></a></li>
                <li><a class="yelp text-white" href="https://www.yelp.com/biz/custom-stone-work-and-rockyard-graford" target="_blank"><i class="fab fa-yelp"></i></a></li>
                <li><a class="instagram text-white" href="https://www.instagram.com/customstoneworkpk/" target="_blank"><i class="fab fa-instagram no-margin-right" aria-hidden="true"></i></a></li>
                <li><a class="text-white" href="https://www.google.com/maps/place/Custom+Stone+Work+LTD+and+Rock+Yard/@32.909721,-98.436538,15z/data=!4m5!3m4!1s0x0:0x73feaabf557d0b88!8m2!3d32.909721!4d-98.436538" target="_blank"><i class="fas fa-map-marker-alt"></i></a></li>
              </ul>
            </div>
            <!-- end social media -->
            <!-- start copyright -->
            <div class="text-left text-small xs-text-center">&copy; 2018 Custom Stone Work, Ltd. <br /><a href="https://www.biblegateway.com/passage/?search=matthew+6%3A25-33&version=NKJV" target="_blank" class="text-dark-gray">Matt 6:33</a></div>
            <!-- end copyright -->
          </div>
          <!-- start additional links -->
          <div class="col-md-4 col-sm-6 col-xs-12 widget border-right border-color-medium-dark-gray padding-45px-left sm-padding-15px-left sm-no-border-right sm-margin-30px-bottom xs-text-center">
            <div class="widget-title alt-font text-small text-medium-gray text-uppercase margin-10px-bottom font-weight-600">Additional Links</div>
            <ul class="list-unstyled">
              <li><a class="text-small" href="stone-mason.html">Custom Stone Work</a></li>
              <li><a class="text-small" href="portfolio.html">Portfolio</a></li>
              <li><a class="text-small" href="the-rockyard-on-possum-kingdom.html">The Rockyard</a></li>
              <li><a class="text-small" href="rockyard-products.html">Products</a></li>
              <li><a class="text-small" href="contact.php">Contact</a></li>
            </ul>
          </div>
          <!-- end additional links -->
          <!-- start contact information -->
          <div class="col-md-4 col-sm-6 col-xs-12 widget border-right border-color-medium-dark-gray padding-45px-left sm-padding-15px-left sm-clear-both sm-no-border-right  xs-margin-30px-bottom xs-text-center">
            <div class="widget-title alt-font text-small text-medium-gray text-uppercase margin-10px-bottom font-weight-600">Contact Info</div>
            <p class="text-small display-block margin-15px-bottom width-80 xs-center-col">Custom Stone Work and Rockyard<br> 300 N FM 2353, Graford, TX 76449</p>
            <div class="text-small">Email: <a href="mailto:customstoneworkpk@gmail.com">customstoneworkpk@gmail.com</a></div>
            <div class="text-small">Phone: <a href="tel:19407793700">+1 (940) 779 3700</a></div>
            <a href="https://www.google.com/maps/place/Custom+Stone+Work+LTD+and+Rock+Yard/@32.909721,-98.436538,15z/data=!4m5!3m4!1s0x0:0x73feaabf557d0b88!8m2!3d32.909721!4d-98.436538" class="text-small text-uppercase text-decoration-underline">Directions</a>
          </div>
          <!-- end contact information -->
        </div>
      </div>
    </div>
  </footer>
  <!-- end footer -->
  <!-- javascript libraries -->
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/modernizr.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
  <script type="text/javascript" src="js/skrollr.min.js"></script>
  <script type="text/javascript" src="js/smooth-scroll.js"></script>
  <script type="text/javascript" src="js/jquery.appear.js"></script>
  <!-- menu navigation -->
  <script type="text/javascript" src="js/bootsnav.js"></script>
  <script type="text/javascript" src="js/jquery.nav.js"></script>
  <!-- animation -->
  <script type="text/javascript" src="js/wow.min.js"></script>
  <!-- page scroll -->
  <script type="text/javascript" src="js/page-scroll.js"></script>
  <!-- swiper carousel -->
  <script type="text/javascript" src="js/swiper.min.js"></script>
  <!-- parallax -->
  <script type="text/javascript" src="js/jquery.stellar.js"></script>
  <!-- magnific popup -->
  <script type="text/javascript" src="js/jquery.magnific-popup.min.js"></script>
  <!-- portfolio with shorting tab -->
  <script type="text/javascript" src="js/isotope.pkgd.min.js"></script>
  <!-- images loaded -->
  <script type="text/javascript" src="js/imagesloaded.pkgd.min.js"></script>
  <!-- pull menu -->
  <script type="text/javascript" src="js/classie.js"></script>
  <!-- fitvids (image loading) -->
  <script type="text/javascript" src="js/jquery.fitvids.js"></script>
  <!-- equalize -->
  <script type="text/javascript" src="js/equalize.min.js"></script>
  <!-- quote form -->
  <script type="text/javascript" src="js/quoteform.js"></script>
  <!-- setting -->
  <script type="text/javascript" src="js/main.js"></script>
</body>

</html>
