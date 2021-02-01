<?php
defined('ABSPATH') OR exit;

$email = get_option( 'admin_email' );
$partner = 'wp-generic';
$affiliate = '221269';
$platformId = '5';
$arrHost = parse_url(home_url('', $scheme = 'http'));
$host = $arrHost['host'];

$endpoint = "https://www.trustedsite.com";
?>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

<div class="wrap" id="mcafeesecure-container">

    <div id="mcafeesecure-data" data-host="<?php echo $host; ?>" data-email="<?php echo $email; ?>"></div>

    <div id="mcafeesecure-activation" style="display:none;">
        <h1>McAfee SECURE</h1>
        <br/>
        <div id="signup-header">Your Account</div>
        <div id="signup-text">To activate the McAfee SECURE service, please create your TrustedSite account. </div>

        <form>
            <span id="email">Email
            <input id="email-input" class="mfs-input" type="text" name="email" value="<?php echo get_option('admin_email')?>"></span><br>
            <span id="domain">Domain
            <input id="domain-input" class="mfs-input" type="text" name="domain" value="<?php echo get_option('siteurl')?>"></span><br><br>
            <input type="submit" name="submit" value="Create Account" id="activate-now">
        </form>
    </div>

    <div id="mcafeesecure-dashboard">
        <h1>McAfee SECURE</h1>
        <div class="wrapper">
            <div id="content">

              <div class="row-wrapper">
                  <div class="row" id="summary">
                      <div class="mfs-title">
                          <span class="status-icon"></span>
                          Summary
                      </div>
                  </div>
              </div>

                <div class="row-wrapper">
                    <div class="row" id="security">
                        <div class="mfs-arrow">
                            <i class="fa fa-angle-right"></i>
                        </div>
                        <div class="link">View Details</div>

                        <div class="mfs-row">
                            <span class="status-icon"></span>
                            Security
                        </div>
                    </div>
                </div>

                <div class="row-wrapper">
                    <div class="row" id="floating-trustmark">
                        <div class="mfs-arrow">
                            <i class="fa fa-angle-right"></i>
                        </div>
                        <div class="link">View Details</div>

                        <div class="mfs-row">
                            <span class="status-icon"></span>
                            Floating trustmark
                        </div>
                    </div>
                </div>

                <div class="row-wrapper">
                    <div class="row" id="directory-listing">
                        <div class="mfs-arrow">
                            <i class="fa fa-angle-right"></i>
                        </div>
                        <div class="link">View Details</div>

                        <div class="mfs-row">
                            <span class="status-icon"></span>
                            Directory listing
                        </div>
                    </div>
                </div>

                <div class="row-wrapper">
                    <div class="row" id="search-highlighting">
                        <div class="mfs-arrow">
                            <i class="fa fa-angle-right"></i>
                        </div>
                        <div class="link">View Details</div>

                        <div class="mfs-row">
                            <span class="status-icon"></span>
                            Search highlighting
                        </div>
                    </div>
                </div>

                <div class="row-wrapper">
                    <div class="row" id="engagement-trustmark">
                        <div class="mfs-arrow">
                            <i class="fa fa-angle-right"></i>
                        </div>
                        <div class="link">View Details</div>

                        <div class="mfs-row">
                            <span class="status-icon"></span>
                            Engagement trustmark
                        </div>
                    </div>
                </div>

                <div class="row-wrapper">
                    <div class="row" id="sip">
                        <div class="mfs-arrow">
                            <i class="fa fa-angle-right"></i>
                        </div>
                        <div class="link">View Details</div>

                        <div class="mfs-row">
                            <span class="status-icon"></span>
                            Shopper Identity Protection
                        </div>
                    </div>
                </div>

                <div class="row-wrapper">
                    <div class="row" id="search-submission">
                        <div class="mfs-arrow">
                            <i class="fa fa-angle-right"></i>
                        </div>
                        <div class="link">View Details</div>

                        <div class="mfs-row">
                            <span class="status-icon"></span>
                            Search submission
                        </div>
                    </div>
                </div>

                <div class="row-wrapper">
                    <div class="row" id="diagnostics">
                        <div class="mfs-arrow">
                            <i class="fa fa-angle-right"></i>
                        </div>
                        <div class="link">View Details</div>

                        <div class="mfs-row">
                            <span class="status-icon"></span>
                            Diagnostics
                        </div>
                    </div>
                </div>
                    
            </div>

            <div id="mcafeesecure-sideframe" style="display:none;">
                <div id="mcafeesecure-upgrade">

                    <div id="mcafeesecure-pro" style="display:none;">
                        <img id="mfs-logo" src="<?php echo plugins_url('../images/mcafee-secure-trustmark.svg',__FILE__)?>" >
                        <div>
                          <strong>Upgrade to Pro for</strong><br>
                            Unlimited visits</br>            
                            Inline engagement trustmark</br>
                            Shopper Identity Protection<br>
                            <em>and more...</em>
                            <br><br>
                        </div>
                        <form action="<?php echo $endpoint ?>/user/site/<?php echo $host ?>/upgrade" method="get" target="_blank">
                        <button class="upgrade-button" type="submit">Upgrade</button>
                    </div>
                    
                    <div id="mcafeesecure-engage" style="display:none;">
                        <strong>Add the engagement trustmark</strong><br><br>
                        <img id="mfs-engage-mark" src="<?php echo plugins_url('../images/engagement.svg',__FILE__)?>" ><br><br>
                        <div style="text-align:left;">
                            For posts or pages, add the shortcode:<br>
                            <div class="mfs-copybox">
                                <pre>[mcafee-secure]</pre>
                            </div> <br>
                            For template files, add the following:<br>
                            <div class="mfs-copybox">
                                <pre><?php
                                $code = '<?php echo Mcafeesecure::engagement_shortcode(96) ?>';
                                echo htmlspecialchars($code);
                                ?></pre>
                            </div> <br>
                            <a id="engage-learn-more" target="_blank" href="https://support.mcafeesecure.com/hc/en-us/articles/206073486-Adding-the-Engagement-Trustmark-to-a-Wordpress-Site">Learn more</a>
                        </div>
                    </div>
                </div>

                <div class="ts-logo" id="ts-logo-sideframe">
                    <a href="https://www.mcafeesecure.com/trustedsite" target="_blank">
                         <img src="<?php echo plugins_url('../images/operated-by-trustedsite-colored-vertical-center.svg',__FILE__)?>" > 
                     </a>
                </div>      
            </div>
        </div>

        <div class="ts-logo" id="ts-logo-noframe" style="display:none;">
            <a href="https://www.mcafeesecure.com/trustedsite" target="_blank">
                 <img src="<?php echo plugins_url('../images/operated-by-trustedsite-colored-vertical-center.svg',__FILE__)?>" > 
             </a>
        </div>
    </div>     
</div>
