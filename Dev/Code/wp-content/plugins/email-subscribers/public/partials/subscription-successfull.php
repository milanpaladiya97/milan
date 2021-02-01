<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	$es_page_request = $_GET['es'];

	$blogname = get_option( 'blogname' );
	$noerror  = true;
	$home_url = home_url( '/' );
	?>
    <html>
    <head>
        <title><?php echo $blogname; ?></title>
        <meta http-equiv="refresh" content="10; url=<?php echo $home_url; ?>" charset="<?php echo esc_attr( get_option( 'blog_charset' ) ); ?>"/>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
        <meta name="viewport" content="width=device-width" />
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel='stylesheet' id='qode_startit_google_fonts-css'  href='https://fonts.googleapis.com/css?family=Raleway%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRaleway%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&#038;subset=latin%2Clatin-ext&#038;ver=1.0.0' type='text/css' media='all' />

		<?php do_action( 'es_message_head' ); ?>

     <style type="text/css">

            .es_center_info {
                margin: auto;
                width: 100% !important;
                padding: 10px !important;
                text-align: center !important;
                box-sizing: border-box !important;
            }
             .thankyou_content {
                margin: 0 auto;
                padding: 80px 0px 80px !important;
                max-width: 820px;
             }
            .thankyou_content .thankyou_title {
                    font-size: 6.25rem;
                  text-align: center;
                      font-family: roboto !important;
            }
            .thankyou_content .cntent {
                    margin: 0 auto;
                    max-width: 820px;
                  text-align: center;
            }
            .thankyou_content .main-content__checkmark {
                    font-size: 4.0625rem;
                    line-height: 1;
                    color: #24b663;
            }
            .thankyou_content .thankyou_text {
                    margin: 20px 0 0;
                    font-size: 30px !important;
                    line-height: 1.4;
                    margin-bottom: 50px !important;
                    font-family: roboto !important;
                    color: black;
            }
            .fa-check:before {
                content: "\f00c" !important;
            }
            @media (max-width: 767px){
                .thankyou_content .thankyou_title {font-size: 32px;}
                .thankyou_content .thankyou_text {font-size: 18px !important;}
                .logo_img img {width: 100%;max-width: 210px;height: auto;}
                .thankyou_content {padding: 0px 0px 0px !important;}
                .es_center_info {width: 100%;height: auto;display: flex;align-items: center;flex-wrap: wrap;}
                .logo_img {text-align: center;}
                .es_center_info h1 {width: 100%;}
            }

             @media (max-width: 400px){
                .logo_img img {width: 100%;}
            }


        </style>
    </head>
    <body>
    <div class="es_center_info es_successfully_subscribed">
        <h1> <?php echo $message; ?> </h1>
    </div>
    </body>
    </html>
	<?php

die();