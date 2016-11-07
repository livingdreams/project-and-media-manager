<?php
//var_dump($client->username);
//var_dump($client["username"]);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <title>Message from {shop_name}</title>

        <style>	@media only screen and (max-width: 300px){ 
                body {
                    width:218px !important;
                    margin:auto !important;
                }
                .table {width:195px !important;margin:auto !important;}
                .logo, .titleblock, .linkbelow, .box, .footer, .space_footer{width:auto !important;display: block !important;}		
                span.title{font-size:20px !important;line-height: 23px !important}
                span.subtitle{font-size: 14px !important;line-height: 18px !important;padding-top:10px !important;display:block !important;}		
                td.box p{font-size: 12px !important;font-weight: bold !important;}
                .table-recap table, .table-recap thead, .table-recap tbody, .table-recap th, .table-recap td, .table-recap tr { 
                    display: block !important; 
                }
                .table-recap{width: 200px!important;}
                .table-recap tr td, .conf_body td{text-align:center !important;}	
                .address{display: block !important;margin-bottom: 10px !important;}
                .space_address{display: none !important;}	
            }
            @media only screen and (min-width: 301px) and (max-width: 500px) { 
                body {width:308px!important;margin:auto!important;}
                .table {width:285px!important;margin:auto!important;}	
                .logo, .titleblock, .linkbelow, .box, .footer, .space_footer{width:auto!important;display: block!important;}	
                .table-recap table, .table-recap thead, .table-recap tbody, .table-recap th, .table-recap td, .table-recap tr { 
                    display: block !important; 
                }
                .table-recap{width: 295px !important;}
                .table-recap tr td, .conf_body td{text-align:center !important;}

            }
            @media only screen and (min-width: 501px) and (max-width: 768px) {
                body {width:478px!important;margin:auto!important;}
                .table {width:450px!important;margin:auto!important;}	
                .logo, .titleblock, .linkbelow, .box, .footer, .space_footer{width:auto!important;display: block!important;}			
            }
            @media only screen and (max-device-width: 480px) { 
                body {width:308px!important;margin:auto!important;}
                .table {width:285px;margin:auto!important;}	
                .logo, .titleblock, .linkbelow, .box, .footer, .space_footer{width:auto!important;display: block!important;}

                .table-recap{width: 295px!important;}
                .table-recap tr td, .conf_body td{text-align:center!important;}	
                .address{display: block !important;margin-bottom: 10px !important;}
                .space_address{display: none !important;}	
            }
        </style>

    </head>
    <body style="-webkit-text-size-adjust:none;background-color:#fff;width:650px;font-family:Open-sans, sans-serif;color:#555454;font-size:13px;line-height:18px;margin:auto">
        <table class="table table-mail" style="width:100%;margin-top:10px;-moz-box-shadow:0 0 5px #afafaf;-webkit-box-shadow:0 0 5px #afafaf;-o-box-shadow:0 0 5px #afafaf;box-shadow:0 0 5px #afafaf;filter:progid:DXImageTransform.Microsoft.Shadow(color=#afafaf,Direction=134,Strength=5)">
            <tr>
                <td class="space" style="width:20px;padding:7px 0">&nbsp;</td>
                <td align="center" style="padding:7px 0">
                    <table class="table" bgcolor="#ffffff" style="width:100%">
                        <tr>
                            <td align="center" class="logo" style="border-bottom:4px solid #333333;padding:7px 0">
                                <a title="GlobalDisasterRestoration" href="<?php echo get_home_url(); ?>" style="color:#337ff1">
                                    <img src="http://www.gedrusa.com/wp-content/uploads/2016/07/globalEnterprisesOfSouthFlorida.png" style="max-width:200px;" alt="globalEnterprisesDisasterRestoration" />
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td align="center" class="titleblock" style="padding:7px 0">
                                <font size="2" face="Open-sans, sans-serif" color="#555454">
                                <span class="title" style="font-weight:500;font-size:28px;text-transform:uppercase;line-height:33px">Hi <?php echo $user->user_login; ?>,</span><br/>
                                <span class="subtitle" style="font-weight:500;font-size:16px;text-transform:uppercase;line-height:25px">WELCOME TO YOUR FRANCHISE ADMIN PORTAL </span>
                                </font>
                            </td>
                        </tr>
                        <tr>
                            <td class="space_footer" style="padding:0!important">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
                                <table class="table" style="width:100%">
                                    <tr>
                                        <td width="10" style="padding:7px 0">&nbsp;</td>
                                        <td style="padding:7px 0">
                                            <font size="2" face="Open-sans, sans-serif" color="#555454">
                                            <p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">
                                                "FRANCHISEE” LOGIN INFORMATION:</p>
                                            <span style="color:#777">

                                                <span style="color:#333"><strong>User Name: </strong></span><?php echo $user->user_login; ?><br />
                                                <span style="color:#333"><strong>To set your password, visit the following address: </strong></span><br>
												<a href="<?php echo network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') ?>"><?php echo network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') ?></a> <br />
                                            </span>
                                            <p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;padding-bottom:10px">
                                                <span style="color:#333"><strong>Portal Access: </strong></span><br><a href="<?php echo wp_login_url() ?>" target="_blank" style="color: #2a71b9; text-decoration: none">
                                                    Click here</a> to add and edit your projects. Remember to download our free Disaster Restoration Mobile App   to review your projects from your <a href="https://itunes.apple.com/us/app/disaster-restoration/id649725393?mt=8" target="_blank" style="color: #2a71b9; text-decoration: none">IPhone</a> or <a href="#" target="_blank" style="color: #2a71b9; text-decoration: none">Android </a>device.
                                            </p>

                                            </font>
                                        </td>
                                        <td width="10" style="padding:7px 0">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="space_footer" style="padding:0!important">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="footer" style="border-top:4px solid #333333;padding:7px 0">
                                <em>Copyright &copy; 2016 Global Enterprises Disaster Restoration, All rights reserved.</em>
                                <br />
                                Call us on 1800.725.7045
                                <br />
                                <strong>Contact Us:</strong> <a href="mailto:info@gedrusa.com" target="_top" style="color:#2a71b9;text-decoration:none">:info@gedrusa.com</a>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="space" style="width:20px;padding:7px 0">&nbsp;</td>
            </tr>
        </table>
    </body>
</html>