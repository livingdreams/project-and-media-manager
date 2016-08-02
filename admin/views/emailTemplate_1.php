<?php
//var_dump($client->username);
//var_dump($client["username"]);
?>
<!DOCTYPE html >
<html>
    <head>
              
        <title>Welcome</title>
		<style type="text/css">

			#outlook a{padding:0;} 
			body{
                width:100% !important;
                font-family: 'Lato', Helvetica, Arial, Lucida, sans-serif;
                font-weight: 300;
                color:#666;
            }
            body a{
                color: #2a71b9;
                text-decoration: none;
            }
			.ReadMsgBody{width:100%;} 
			.ExternalClass{width:100%;} 
			body{-webkit-text-size-adjust:none;}
			body{margin:0; padding:0;}
			img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
			table td{border-collapse:collapse;}
			#backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}

			body, #backgroundTable{
				 background-color:#fff;
			}
			#templateContainer{
				border: 1px solid #DDDDDD;
			}
            #mainContentContainer{
                background-color:#FAFAFA;
                text-align: left;
                width:100%;
                padding: 50px;    
                
            }
            #mainContentContainer th{
                border-bottom: 2px solid #000;
                
            }
            
            #mainContentContainer th,td{
                padding: 15px;
                text-align: left;
            }
            #mainContentContainer h2.tabletitle{
                font-size: 21px;
                font-weight: 500;
                text-align: center; 
                                   
            }
            #mainContentContainer h3.tablesubHeading{
                text-align: left;
                font-size: 21px;
                color: #afafaf;
            }
            #mainContentContainer span.columnheading{
                    font-size: 14px;
                    font-weight: 600;
                    color: #474747;               
            }#mainContentContainer a{
                color: #2a71b9;
                font-weight: bold;
            }
            .text{
                padding: 50px;         
                
            }
            #templateFooter{
				  background-color:#FFFFFF;
				  border-top:0;
			}
			.footerContent div{
				  color:#707070;
				  font-family:Arial;
				  font-size:12px;
				  line-height:125%;
				  text-align:left;
			}
			.footerContent div a:link, .footerContent div a:visited, /* Yahoo! Mail Override */ .footerContent div a .yshortcuts /* Yahoo! Mail Override */{
				  color:#336699;
				  font-weight:normal;
				  text-decoration:underline;
			}
			.footerContent img{
				display:inline;
			}
			h1, .h1{
				  color:#202020;
				display:block;
				  font-family:Arial;
				  font-size:34px;
				  font-weight:bold;
				  line-height:100%;
				margin-top:0;
				margin-right:0;
				margin-bottom:10px;
				margin-left:0;
				  text-align:center;
			}
			h2, .h2{
				  color:#202020;
				display:block;
				  font-family:Arial;
				  font-size:16px;
				  font-weight:300;
				  line-height:100%;
				margin-top:0;
				margin-right:0;
				margin-bottom:10px;
				margin-left:0;
				  text-align:center;
			}
			h3, .h3{
				  color:#202020;
				display:block;
				  font-family:Arial;
				  font-size:26px;
				  font-weight:bold;
				  line-height:100%;
				margin-top:0;
				margin-right:0;
				margin-bottom:10px;
				margin-left:0;
				  text-align:center;
			}
			h4, .h4{
				  color:#202020;
				display:block;
				  font-family:Arial;
				  font-size:22px;
				  font-weight:bold;
				  line-height:100%;
				margin-top:0;
				margin-right:0;
				margin-bottom:10px;
				margin-left:0;
				  text-align:left;
			}

			#templatePreheader{
				  background-color:#FAFAFA;
			}
			#templateHeader{
				  background-color:#FFFFFF;
				  border-bottom:0;
			}.headerContent{
				  color:#202020;
				  font-family:Arial;
				  font-size:34px;
				  font-weight:bold;
				  line-height:100%;
				  padding:0;
				  text-align:center;
				  vertical-align:middle;
			}
			
			.headerContent a:link, .headerContent a:visited, /* Yahoo! Mail Override */ .headerContent a .yshortcuts /* Yahoo! Mail Override */{
				  color:#336699;
				  font-weight:normal;
				  text-decoration:underline;
			}
			
			#headerImage{
				height:auto;
				max-width:600px;
			}
			#templateContainer, .bodyContent{
				  background-color:#FFFFFF;
			}
			.bodyContent div{
				  color:#505050;
				  font-family:Arial;
				  font-size:14px;
				  line-height:150%;
				  text-align:left;
			}
			.bodyContent div a:link, .bodyContent div a:visited, /* Yahoo! Mail Override */ .bodyContent div a .yshortcuts /* Yahoo! Mail Override */{
				  color:#336699;
				  font-weight:normal;
				  text-decoration:underline;
			}
			.bodyContent img{
				display:inline;
				height:auto;
			}
			
		</style>
	</head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateContainer">
            <tr>
                <td align="center" valign="top">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateHeader">
                        <tr>
                            <td class="headerContent">
                                <img src="http://www.globalenterprisesouthflorida.com/wp-content/uploads/2016/07/globalEnterprisesOfSouthFlorida.png" style="max-width:200px;" />
                            </td>
                       </tr>
                    </table>
                    <h1><?php echo ucfirst($client->get_fullname());?></h1>
                    <h2>THANK YOU FOR CREATING A ACCOUNT AT GLOBAL DISASTER RESTORATION. </h2>
                </td>
            </tr>
            <br>
        </table>
        <table id="mainContentContainer" >
            <tr background-color="#fafafa">
                <th align="" valign="top">
                    <h2 class="tabletitle">YOUR GLOBAL DISASTER RESTORATION LOGIN DETAILS</h3>
                </th>
            </tr>
            <tr>
                <td><h3 class="tablesubHeading">Here are your login details:</h3></td>
            </tr>
            <tr>
                <td>
                    <span class="columnheading">Track the progress of the work please visit the Client Login area at: </span><a href="<?php  echo get_home_url();?>"> <?php  echo get_home_url();?> </a>
                </td>
            </tr>
             <tr> 
                
                 <td><span class="columnheading">User Name: </span> <?php echo $client->username; ?></td>
             </tr>
             <tr> 
                
                 <td><span class="columnheading">E-mail address: </span> <a href="mailto:<?php echo $client->email; ?>" target="_top">:<?php echo $client->email; ?></a></td>
             </tr>
            <tr>
                <td><span class="columnheading">Password:</span><?php echo $client->password;?> </td>
            </tr>
        </table>
        <div class="text">
          You can also <a href="https://itunes.apple.com/us/app/disaster-restoration/id649725393?mt=8" target="_blank">download our new Mobile App</a> to your phone and get access to tracking the progress at all times, using the same credentials provided. 
            App available on IOS devices only for now. Android version will be available soon.
            <br/>Go to the App Store and <a href="https://itunes.apple.com/us/app/disaster-restoration/id649725393?mt=8"> download the Free Disaster Restoration App. </a>

            <p>To learn how to use the App go view the video tutorial <a href="<?php $video_link ?> "> here  </a> </p>

            
        <table id="mainContentContainer" >
            <tr background-color="#fafafa">
                <th align="" valign="top">
                    <h2 class="tabletitle">Thank you for giving us the opportunity to serve you</h2>
                </th>
            </tr>
            <tr>
                <td><h3 class="tablesubHeading">Few important features in the Client Area</h3></td>
            </tr>
             <tr>
                 <td>
                        <li><span class="columnheading">My Project Status: </span> Section that keeps you informed and clearly indicates how far we’ve progressed with your restoration.</li>  
                 </td>
            </tr>
             <tr>
                 <td>
                     <li><span class="columnheading">Project Documents:</span> Quickly access and download documents and related to your project. Including your Contract, Work Authorization, Customer Selection Form and other miscellaneous documents.</li>
                 </td>
            </tr>
             <tr>
                 <td>
                     <li><span class="columnheading">More Details :</span>You’ll find more details, including dated notes and additional information about your project.</li>
                        <ul>
                            <li><span class="columnheading">Gallery :</span> Easily browse high quality photos from the job site an attractive pop-up photo gallery</li>
                            <li><span class="columnheading">Videos :</span> Easily browse videos related to each milestone.</li>
                     </ul>
                </td>
            </tr>
             <tr>
                 <td>
                     
                 </td>
             </tr>
        </table>
        
    <h3 class="tablesubHeading">Also, please fill out our <a href="<?php echo $commercial_link ?>"> “RED ALERT PROGRAM”</a> form</h3>
<table>
        <tr>
            <td valign="top" width="50%" class="leftColumnContent">
               <table border="0" cellpadding="20" cellspacing="0" width="100%">
                 <tr mc:repeatable>
                   <td valign="top">
                    <div mc:edit="tiwc300_content00">
 	                  <h4 class="h4">Red Alert Program - Residential or Commercial</h4>
                        By joining to our red alert program for your home, you minimize further damages by having an immediate plan of action. Knowing what to do and what to expect in advance is the key to timely mitigation and can help minimize how water and fire or even storm damage can affect your home. 
                     </div>
                    </td>
                 </tr>
                </table>
            </td>
            <td valign="top" width="50%" class="rightColumnContent">
              <table border="0" cellpadding="20" cellspacing="0" width="100%">
                <tr mc:repeatable>
                  <td valign="top">
                    <div mc:edit="tiwc300_content01">
 	                  <h4 class="h4">Advantage of our red alert program</h4>
                        <ul>
                            <li>It would take a little time to complete the form but it will save a lot of time if it’s ever needed.</li>
                            <li>You will know who to rely on when disaster happens and not to think about “What to do now?”</li>
                            <li>When disaster happens, our team will be there for you, we are well prepared to protect your property and mitigate your damages.</li>
                            <li>Providing detailed information about your home or business will avoid questions which require immediate answers. This saves time and money. </li>
                        </ul>
                    </div>
                 </td>
               </tr>
             </table>
            </td>
          </tr>
       </table>
<table border="0" cellpadding="10" cellspacing="0" width="100%" id="templateFooter">
  <tr>
    <td valign="top" class="footerContent">
       <table border="0" cellpadding="10" cellspacing="0" width="100%">
         <tr>
            <td valign="top" width="350">
              <div mc:edit="std_footer">
				<em>Copyright &copy; 2016 Global Enterprises Disaster Restoration, All rights reserved.</em>
				<br />
				Call us on 1800.725.7045
				<br />
				<strong>Contact Us:</strong> <a href="info@globalenterprisesouthflorida.com" target="_top">info@globalenterprisesouthflorida.com</a>
              </div>
            </td>
         </tr>
    </table>
    </td>
  </tr>
</table>
    </body>
</html>
