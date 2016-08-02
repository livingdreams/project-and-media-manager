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
								<a title="GlobalDisasterRestoration" href="<?php  echo get_home_url();?>" style="color:#337ff1">
									<img src="http://www.globalenterprisesouthflorida.com/wp-content/uploads/2016/07/globalEnterprisesOfSouthFlorida.png" style="max-width:200px;" alt="globalEnterprisesDisasterRestoration" />
								</a>
							</td>
						</tr>

<tr>
	<td align="center" class="titleblock" style="padding:7px 0">
		<font size="2" face="Open-sans, sans-serif" color="#555454">
			<span class="title" style="font-weight:500;font-size:28px;text-transform:uppercase;line-height:33px">Hi <?php echo ucfirst($client->get_fullname());?>,</span><br/>
			<span class="subtitle" style="font-weight:500;font-size:16px;text-transform:uppercase;line-height:25px">Thank you for creating a client account at Global Enterprises Of Disaster Restoration.</span>
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
							Your Global Enterprises Of Disaster Restoration login details</p>
						<span style="color:#777">
							Here are your login details:<br /> 
							
							<span style="color:#333"><strong>User Name: </strong></span><?php echo $client->username; ?><br />
							<span style="color:#333"><strong>E-mail address: <a href="mailto:<?php echo $client->email; ?>" style="color:#337ff1"><?php echo $client->email; ?></a></strong></span><br />
							<span style="color:#333"><strong>Password: </strong></span> <?php echo $client->password;?> <br />
						</span>
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
	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
		<table class="table" style="width:100%">
			<tr>
				<td width="10" style="padding:7px 0">&nbsp;</td>
				<td style="padding:7px 0">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						<p style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">Few important features in the Client Area</p>
						<ul style="margin-bottom:0">
							<li><span style="color:#333"><strong>My Project Status: </strong></span>Section that keeps you informed and clearly indicates how far we’ve progressed with your restoration.</li>
							<li><span style="color:#333"><strong>Project Documents: </strong></span>Quickly access and download documents and related to your project. Including your Contract, Work Authorization, Customer Selection Form and other miscellaneous documents.</li>
							<li><span style="color:#333"><strong>More Details: </strong></span>You’ll find more details, including dated notes and additional information about your project.</li>							
							<li><span style="color:#333"><strong>Gallery : </strong></span>Easily browse high quality photos from the job site an attractive pop-up photo gallery</li>							
							<li><span style="color:#333"><strong>Videos  : </strong></span>Easily browse videos related to each milestone.</li>							
						</ul>
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
	<td align="center" class="titleblock" style="padding:7px 0">
		<font size="2" face="Open-sans, sans-serif" color="#555454">
			<span class="subtitle" style="font-weight:500;font-size:16px;text-transform:uppercase;line-height:25px">Also, please fill out our <a href="<?php echo $commercial_link ?>" style="color: #2a71b9; text-decoration: none"> “RED ALERT PROGRAM”</a></span>
		</font>
	</td>
</tr>
<tr>
	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
		<table class="table" style="width:100%">
			<tr>
				<td width="10" style="padding:7px 0">&nbsp;</td>
				<td style="padding:7px 0">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						<p style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">Red Alert Program - Residential or Commercial</p>
						<span style="color:#777">By joining to our red alert program for your home, you minimize further damages by having an immediate plan of action. Knowing what to do and what to expect in advance is the key to timely mitigation and can help minimize how water and fire or even storm damage can affect your home.</span>
						<span style="color:#333; margin-top:15px; font-weight:500;">Advantage of our red alert program</span>
						<ul style="margin-bottom:0">
							<li>It would take a little time to complete the form but it will save a lot of time if it’s ever needed.</li>
							<li>You will know who to rely on when disaster happens and not to think about “What to do now?”</li>
							<li>When disaster happens, our team will be there for you, we are well prepared to protect your property and mitigate your damages.</li>
							<li>Providing detailed information about your home or business will avoid questions which require immediate answers. This saves time and money.</li>
						</ul>
					</font>
				</td>
				<td width="10" style="padding:7px 0">&nbsp;</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="linkbelow" style="padding:7px 0">
		<font size="2" face="Open-sans, sans-serif" color="#555454">
			<span style="color:#777">Track the progress of the work please visit the Client Login area at: </span><a href="<?php  echo get_home_url();?> " style="color:#337ff1"><?php  echo get_home_url();?> </a><br>
			<span style="color:#777">You can also </span><a href="https://itunes.apple.com/us/app/disaster-restoration/id649725393?mt=8" target="_blank" style="color: #2a71b9; text-decoration: none">download our new Mobile App</a> <span style="color:#777">to your phone and get access to tracking the progress at all times, using the same credentials provided. 
            App available on IOS devices only for now. Android version will be available soon.</span>
            <br/><span style="color:#777">Go to the App Store and </span><a href="https://itunes.apple.com/us/app/disaster-restoration/id649725393?mt=8" style="color: #2a71b9; text-decoration: none"> download the Free Disaster Restoration App. </a>

		</font>
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
				<strong>Contact Us:</strong> <a href="info@globalenterprisesouthflorida.com" target="_top" style="color: #2a71b9; text-decoration: none">info@globalenterprisesouthflorida.com</a>
							</td>
						</tr>
					</table>
				</td>
				<td class="space" style="width:20px;padding:7px 0">&nbsp;</td>
			</tr>
		</table>
	</body>
</html>