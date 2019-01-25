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
    if (verifyFormToken('form1')) {

        // CHECK TO SEE IF THIS IS A MAIL POST
        if (isset($_POST['URL-main'])) {

            // Building a whitelist array with keys which will send through the form, no others would be accepted later on
            $whitelist = array('token','name','email','phone', 'comment');

            // Building an array with the $_POST-superglobal
            foreach ($_POST as $key=>$item) {

                    // Check if the value $key (fieldname from $_POST) can be found in the whitelisting array, if not, die with a short message to the hacker
            		if (!in_array($key, $whitelist)) {

            			writeLog('Unknown form fields');
            			die("Hack-Attempt detected. Please use only the fields in the form");

            		}
            }

            //MESSAGE body

            $message = "
           	<html>
           	<head>
           	<title>HTML email</title>
           	</head>
           	<body>
           	<table width='50%' border='0' align='center' cellpadding='0' cellspacing='0'>
           	<tr>
           	<td colspan='2' align='center' valign='top'><img style=' margin-top: 15px; ' src='http://www.customstoneworkpk.com/images/csw-logo-black.png' ></td>
           	</tr>
           	<tr>
           	<td width='50%' align='right'>&nbsp;</td>
           	<td align='left'>&nbsp;</td>
           	</tr>
           	<tr>
           	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Name:</td>
           	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>".strip_tags($_POST['name'])."</td>
           	</tr>
           	<tr>
           	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Email:</td>
           	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>".strip_tags($_POST['email'])."</td>
           	</tr>
           		<tr>
           	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Phone:</td>
           	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>".strip_tags($_POST['phone'])."</td>
           	</tr>
           	<tr>
           	<td align='right' valign='top' style='border-top:1px solid #dfdfdf; border-bottom:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Message:</td>
           	<td align='left' valign='top' style='border-top:1px solid #dfdfdf; border-bottom:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>".nl2br(strip_tags($_POST['comment']))."</td>
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

      			$subject = 'Contact Form Submission';

      			$headers = "From: " . $cleanedFrom . "\r\n";
      			$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
      			$headers .= "MIME-Version: 1.0\r\n";
      			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            if (mail($to, $subject, $message, $headers)) {
              //thank you redirect
               header('Location: contact-thank-you.html');
            } else {
              echo 'There was a problem sending the email.';
            }

            // DON'T BOTHER CONTINUING TO THE HTML...
            die();

        }
      } else {

     		if (!isset($_SESSION[$form.'_token'])) {

      	} else {
      	echo "Hack-Attempt detected. Got ya!.";
   			writeLog('Formtoken');
   	    }

    	}

?>



<!doctype html>
<html class="no-js" lang="en">
    <head>
        <!-- title -->
        <title>Custom Stone Work | Contact</title>
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
	    $newToken = generateFormToken('form1');
    ?>

    <body>
        <!-- start header -->
        <header>
          <!-- start navigation -->
          <nav class="navbar navbar-default bootsnav navbar-top header-dark bg-transparent nav-box-width white-link">
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
                        <a href="stone-mason.html">Custom Stone Work</a>
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
                                <li><a href="gravel.html">Gravel and Aggregate</a></li>
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
                                  <a href="contact.html#contact" class="text-center bg-black">Send an Inquiry&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></a>
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
                        <a href="contact.html">Contact</a>
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
        <section class="wow fadeIn parallax one-second-screen" data-stellar-background-ratio="0.5" style="background-image:url('images/BUILDERS_SANDSTONE_WITH_MOSS.JPG');">
            <div class=""></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 extra-small-screen display-table page-title-large">
                        <div class="display-table-cell vertical-align-middle text-left sm-display-none">
                            <!-- start page title -->
                            <h1 class="text-white alt-font font-weight-600 letter-spacing-minus-1 margin-15px-bottom padding-three-left">Contact us</h1>
                            <!-- end page title -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end page title section -->
        <!-- start contact info -->
        <section class="wow fadeIn">
            <div class="container">
                <div class="row">
                    <div class="row">
                        <!-- start contact info item -->
                        <div class="col-md-3 col-sm-6 col-xs-12 text-center sm-margin-eight-bottom xs-margin-30px-bottom wow fadeInUp last-paragraph-no-margin">
                            <div class="display-inline-block margin-20px-bottom">
                                <div class="bg-extra-dark-gray icon-round-medium"><i class="icon-map-pin icon-medium text-white"></i></div>
                            </div>
                            <div class="text-extra-dark-gray text-uppercase text-small font-weight-600 alt-font margin-5px-bottom">Visit Our Office</div>
                            <a href="https://www.google.com/maps/place/Custom+Stone+Work+LTD+and+Rock+Yard/@32.909721,-98.436538,15z/data=!4m5!3m4!1s0x0:0x73feaabf557d0b88!8m2!3d32.909721!4d-98.436538" class="center-col"><p>300 N FM 2353<br>Graford, TX, 76449</p></a>
                            <a href="https://www.google.com/maps/place/Custom+Stone+Work+LTD+and+Rock+Yard/@32.909721,-98.436538,15z/data=!4m5!3m4!1s0x0:0x73feaabf557d0b88!8m2!3d32.909721!4d-98.436538" class="text-uppercase text-deep-pink text-small margin-15px-top xs-margin-10px-top display-inline-block">GET DIRECTIONS</a>
                        </div>
                        <!-- end contact info item -->
                        <!-- start contact info item -->
                        <div class="col-md-3 col-sm-6 col-xs-12 text-center sm-margin-eight-bottom xs-margin-30px-bottom wow fadeInUp last-paragraph-no-margin" data-wow-delay="0.2s">
                            <div class="display-inline-block margin-20px-bottom">
                                <div class="bg-extra-dark-gray icon-round-medium"><i class="icon-chat icon-medium text-white"></i></div>
                            </div>
                            <div class="text-extra-dark-gray text-uppercase text-small font-weight-600 alt-font margin-5px-bottom">Let's Talk</div>
                            <p class="center-col"><a href="tel:19407793700">Phone: 1-940-779-3700</a></p>
                            <a href="tel:19407793700" class="text-uppercase text-deep-pink text-small margin-15px-top xs-margin-10px-top display-inline-block">call us</a>
                        </div>
                        <!-- end contact info item -->
                        <!-- start contact info item -->
                        <div class="col-md-3 col-sm-6 col-xs-12 text-center xs-margin-30px-bottom wow fadeInUp last-paragraph-no-margin" data-wow-delay="0.4s">
                            <div class="display-inline-block margin-20px-bottom">
                                <div class="bg-extra-dark-gray icon-round-medium"><i class="icon-envelope icon-medium text-white"></i></div>
                            </div>
                            <div class="text-extra-dark-gray text-uppercase text-small font-weight-600 alt-font margin-5px-bottom">E-mail Us</div>
                            <p class="center-col"><a href="mailto:customstoneworkpk@gmail.com">customstoneworkpk@gmail.com</a></p>
                            <a href="mailto:customstoneworkpk@gmail.com" class="text-uppercase text-deep-pink text-small margin-15px-top xs-margin-10px-top display-inline-block">send e-mail</a>
                        </div>
                        <!-- end contact info item -->
                        <!-- start contact info item -->
                        <div class="col-md-3 col-sm-6 col-xs-12 text-center wow fadeInUp last-paragraph-no-margin" data-wow-delay="0.6s">
                            <div class="display-inline-block margin-20px-bottom">
                                <div class="bg-extra-dark-gray icon-round-medium"><i class="icon-clipboard icon-medium text-white"></i></div>
                            </div>
                            <div class="text-extra-dark-gray text-uppercase text-small font-weight-600 alt-font margin-5px-bottom">Request a Quote</div>
                            <p class="xs-width-100 center-col">Tell us about<br>your next project.</p>
                            <a href="#contact" class="inner-link text-uppercase text-deep-pink text-small margin-15px-top xs-margin-10px-top display-inline-block">open ticket</a>
                        </div>
                        <!-- end contact info item -->
                    </div>
                </div>
            </div>
        </section>
        <!-- end contact info section -->
        <!-- start contact form -->
        <section id="contact" class="wow fadeIn no-padding bg-extra-dark-gray">
            <div class="container-fluid">
                <div class="row equalize sm-equalize-auto">
                    <div class="col-md-6 cover-background sm-height-450px xs-height-350px wow fadeIn" style="background: url('images/1200x854-pebbles-stack_JSheehy.JPG')"></div>
                    <div class="col-md-6 wow fadeIn">
                        <div class="padding-seven-all text-center xs-no-padding-lr">
                            <div class="text-medium-gray alt-font text-small text-uppercase margin-5px-bottom xs-margin-three-bottom">Fill out the form and we'll be in touch soon!</div>
                            <h5 class="margin-55px-bottom text-white alt-font font-weight-700 text-uppercase xs-margin-ten-bottom">Tell Us What You Need:</h5>
                            <form id="project-contact-form" name="contactform" action="contact.php" method="post">
                                <input type="hidden" name="token" value="<?php echo $newToken; ?>" />
                                <div class="row">
                                     <div class="col-md-12">
                                        <div id="success-project-contact-form" class="no-margin-lr"></div>
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
                                        <textarea name="comment" id="comment" placeholder="Describe your project" rows="6" class="bg-transparent border-color-medium-dark-gray medium-textarea"></textarea>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <button id="project-contact-us-button" type="submit" class="btn btn-deep-pink btn-medium btn-rounded margin-20px-top">send message</button>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end contact form -->
        <!-- start map section -->
        <section class="no-padding one-second-screen sm-height-400px wow fadeIn"><iframe class="width-100 height-100" <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d53593.8813035191!2d-98.4352818!3d32.9082778!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x86522c5ef3cdc1ab%3A0x224fb9fe1763d396!2s300+N+Fm+2353%2C+Graford%2C+TX+76449!5e0!3m2!1sen!2sus!4v1539013731341" style="border:0" allowfullscreen></iframe></section>
        <!-- end map section -->
        <!-- start social section -->
        <section class="wow fadeIn">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center social-style-1 round social-icon-style-5">
                        <ul class="large-icon no-margin-bottom">
                          <li><a class="facebook text-white" href="https://www.facebook.com/customstoneworkandrockyard" target="_blank"><i class="fab fa-facebook-f" aria-hidden="true"></i></a></li>
                          <li><a class="yelp text-white" href="https://www.yelp.com/biz/custom-stone-work-and-rockyard-graford" target="_blank"><i class="fab fa-yelp"></i></a></li>
                          <li><a class="instagram text-white" href="https://www.instagram.com/customstoneworkpk/" target="_blank"><i class="fab fa-instagram no-margin-right" aria-hidden="true"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!-- end social section -->
        <!-- start footer -->
        <footer class="footer-standard-dark bg-extra-dark-gray">
          <div class="footer-widget-area padding-five-tb xs-padding-30px-tb">
            <div class="container">
              <div class="row equalize xs-equalize-auto">
                <div class="col-md-4 col-sm-6 col-xs-12 widget border-right border-color-medium-dark-gray sm-no-border-right sm-margin-30px-bottom xs-text-center">
                  <!-- start logo -->
                  <a href="index.html" class="margin-20px-bottom display-inline-block"><img class="footer-logo" src="images/csw-logo-white.png" alt="Custom Stone Work"></a>
                  <!-- end logo -->
                  <p class="text-small width-95 xs-width-100">Custom Stone Work and Rockyard has provided high quality masonry services and masonry supplies to the greater Possum Kingdom Lake area for over 25 years. We serve Graford, Graham, Bryson, Breckenridge, Jacksboro, Mineral Wells, and beyond.</p>
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
                    <li><a class="text-small" href="contact.html">Contact</a></li>
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
        <!-- setting -->
        <script type="text/javascript" src="js/main.js"></script>
    </body>
</html>
