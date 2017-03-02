<?php 
// Pear library includes
// You should have the pear lib installed
include_once('/usr/share/php/Mail.php');
include_once('/usr/share/php/Mail/mime.php');
include_once('./config/config.php');

//Settings 
$max_allowed_file_size = 29000; // size in KB 
$allowed_extensions = array("jpg", "jpeg", "gif", "bmp", "doc", "docx", "ppt", "pptx", "pdf", "png");
$upload_folder = './uploads/'; //<-- this folder must be writeable by the script

$your_email = $submissionaddress;//<<--  update this to your email address
// $your_email = 'luke.appleton@exeterguild.com';//<<--  update this to your email address

$errors ='';

if(isset($_POST['submit']))
{
	//Get the uploaded file information
	$name_of_uploaded_file =  basename($_FILES['uploaded_file']['name']);
	
	//get the file extension of the file
	$type_of_uploaded_file = substr($name_of_uploaded_file, 
							strrpos($name_of_uploaded_file, '.') + 1);
	
	$size_of_uploaded_file = $_FILES["uploaded_file"]["size"]/1024;
	
	///------------Do Validations-------------
	if(empty($_POST['name'])||empty($_POST['email']))
	{
		$errors .= "\n Name and Email are required fields. ";	
	}
	if(IsInjected($visitor_email))
	{
		$errors .= "\n Bad email value!";
	}
	
	if($size_of_uploaded_file > $max_allowed_file_size ) 
	{
		$errors .= "\n Size of file should be less than $max_allowed_file_size";
	}
	
	//------ Validate the file extension -----
	$allowed_ext = false;
	for($i=0; $i<sizeof($allowed_extensions); $i++) 
	{ 
		if(strcasecmp($allowed_extensions[$i],$type_of_uploaded_file) == 0)
		{
			$allowed_ext = true;		
		}
	}
	
	if(!$allowed_ext)
	{
		$errors .= "\n The uploaded file is not supported file type. ".
		" Only the following file types are supported: ".implode(',',$allowed_extensions);
	}
	
	//send the email 
	if(empty($errors))
	{
		//copy the temp. uploaded file to uploads folder
		$path_of_uploaded_file = $upload_folder . $name_of_uploaded_file;
		$tmp_path = $_FILES["uploaded_file"]["tmp_name"];
		
		if(is_uploaded_file($tmp_path))
		{
		    if(!copy($tmp_path,$path_of_uploaded_file))
		    {
		    	$errors .= '\n error while copying the uploaded file';
		    }
		}
		
		//send the email
		$name = $_POST['name'];
		$visitor_email = $_POST['email'];
		$user_message = $_POST['message'];
		$copies = $_POST['copies'];
		$papersize = $_POST['papersize'];
		$papertype = $_POST['papertype'];
		$colouredpaper = $_POST['colouredpaper'];
		$printingtone = $_POST['printingtone'];
		$printingtype = $_POST['printingtype'];
		$stapling = $_POST['stapling'];
		$holepunch = $_POST['holepunch'];
		$binding = $_POST['binding'];
		$lamination = $_POST['lamination'];
		$collectfrom = $_POST['collectfrom'];
		
//		$user_message = $message1 . $message2;
		$to = $your_email;
		$subject="New form submission";
		$from = $your_email;
		$text = "A user  $name has sent you this message:\n copies: $copies \n Papersize: $papersize \n Papertype: $papertype \n Stapling: $stapling \n Holepunch: $holepunch \n Binding: $binding \n Lamination: $lamination \n Collectfrom: $collectfrom \n User Message: $user_message";
		
		$message = new Mail_mime(); 
		$message->setTXTBody($text); 
		$message->addAttachment($path_of_uploaded_file);
		$body = $message->get();
		$extraheaders = array("From"=>$from, "Subject"=>$subject,"Reply-To"=>$visitor_email);
		$headers = $message->headers($extraheaders);
		$mail = Mail::factory("mail");
		$mail->send($to, $headers, $body);
		//redirect to 'thank-you page
		header('Location: thank-you.html');
	}
}
///////////////////////////Functions/////////////////
// Function to validate against any email injection attempts
function IsInjected($str)
{
  $injections = array('(\n+)',
              '(\r+)',
              '(\t+)',
              '(%0A+)',
              '(%0D+)',
              '(%08+)',
              '(%09+)'
              );
  $inject = join('|', $injections);
  $inject = "/$inject/i";
  if(preg_match($inject,$str))
    {
    return true;
  }
  else
    {
    return false;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Example Pagbe</title>

<!-- PASTE THESE IN -->
<link href="http://photos.exeterguild.com/developer/workspace/SuperFast/css/SuperFastcore.css" rel="stylesheet" type="text/css" media="all" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<!-- END -->

<!-- a helper script for vaidating the form-->
<script language="JavaScript" src="scripts/gen_validatorv31.js" type="text/javascript"></script>	

</head>





<body>
<?php
if(!empty($errors))
{
	echo nl2br($errors);
}
?>

  <header class="text-center">  
	  <br /><h1 style="background: url(https://www.exeterguild.org/stylesheet/GuildTheme/background-bar-opt.png); background-size: cover; color: white; padding: 20px;"><a href="https://www.exeterguild.org"><img src="https://www.exeterguild.org/stylesheet/GuildTheme/guild-logo.png"></a> <br />Remote Print</h1>
  </header>
  
  <?php echo "<div class=\"container-row wide center-block container-center\">
	<div class=\"item item-1 item-two-column\">"; ?>
<form method="POST" name="email_form_with_php" 
action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data"> 
<p>
	
	
<p>
	<label for='name'>Name: </label><br>
<input type="text" name="name" >
</p>
<p>
<label for='email'>Email: </label><br>
<input type="text" name="email" >
</p>
 <p>Number of copies <input name="copies" required="required" type="number" />
   Papersize 
   <select name="papersize" required="required">
      <option value="Please specify…">
         Please specify&hellip;
      </option>
      <option value="A3">A3</option>
      <option value="A4">A4</option>
      <option value="A5">A5</option>
      <option value="A6">A6</option>
      <option value="4x6” (photopaper only)">4x6&rdquo; (photopaper only)</option>
      <option value="5x7” (photopaper only)">5x7&rdquo; (photopaper only)</option>
      <option value="Other – please specify ">Other &ndash; please specify</option>
   </select>
   If other please specify <input name="Other" type="text" value="other" />Paper type 
   <select name="papertype">
      <option value="Please specify…">Please specify&hellip;</option>
      <option value="80gsm (standard)">80gsm (standard)</option>
      <option value="80gsm coloured">80gsm coloured</option>
      <option value="100gsm (high quality)">100gsm (high quality)</option>
      <option value="140gsm gloss">140gsm gloss</option>
      <option value="160gsm (thin card)">160gsm (thin card)</option>
      <option value="160gsm coloured">160gsm coloured</option>
      <option value="200gsm (standard card)">200gsm (standard card)</option>
      <option value="250gsm (heavy card)">250gsm (heavy card)</option>
      <option value="300gsm (thick card)">300gsm (thick card)</option>
      <option value="350gsm (extra thick card)">350gsm (extra thick card)</option>
      <option value="Photopaper">Photopaper</option>
   </select>
   Coloured Paper
   <select name="colouredpaper">
      <option value="Not applicable">Not applicable</option>
      <option value="Salmon">Salmon</option>
      <option value="Curlew Cream">Curlew Cream</option>
      <option value="Bunting Yellow (pale)">Bunting Yellow (pale)</option>
      <option value="Canary Yellow (deep)">Canary Yellow (deep)</option>
      <option value="Fantail Orange">Fantail Orange</option>
      <option value="Rosella Red">Rosella Red</option>
      <option value="Bullfinch Pink (bright)">Bullfinch Pink (bright)</option>
      <option value="Flamingo Pink (pale)">Flamingo Pink (pale)</option>
      <option value="Skylark Violet (pale)">Skylark Violet (pale)</option>
      <option value="Hummingbird Amethyst (bright)">Hummingbird Amethyst (bright)</option>
      <option value="Plover Purple (deep)">Plover Purple (deep)</option>
      <option value="Kingfisher Blue (deep)">Kingfisher Blue (deep)</option>
      <option value="Puffin Blue (pale)">Puffin Blue (pale)</option>
      <option value="Leafbird Green (pale)">Leafbird Green (pale)</option>
      <option value="Woodpecker Green (deep)">Woodpecker Green (deep)</option>
   </select>
   <span style="font-size:11.0pt;line-height:107%;
      font-family:&quot;Gill Sans&quot;,sans-serif;mso-fareast-font-family:Calibri;mso-fareast-theme-font:
      minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:EN-US;mso-bidi-language:
      AR-SA">
      Printing Tone
      <select name="printingtone">
         <option value="Colour">Colour</option>
         <option value="Monochrome">Monochrome</option>
      </select>
      <span style="font-size:11.0pt;line-height:107%;
         font-family:&quot;Gill Sans&quot;,sans-serif;mso-fareast-font-family:Calibri;mso-fareast-theme-font:
         minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:EN-US;mso-bidi-language:
         AR-SA">Printing type</span> 
      <select name="printingtype">
         <option value="Single-sided">Single-sided</option>
         <option value="Double-sided">Double-sided</option>
         <option value="Pamphlet">Pamphlet</option>
      </select>
      <span style="font-size:11.0pt;line-height:107%;
         font-family:&quot;Gill Sans&quot;,sans-serif;mso-fareast-font-family:Calibri;mso-fareast-theme-font:
         minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:EN-US;mso-bidi-language:
         AR-SA">Stapling</span> 
      <select name="stapling">
         <option value="None">None</option>
         <option value="1 Staple (top left corner)">1 Staple (top left corner)</option>
         <option value="2 Staples (left margin)">2 Staples (left margin)</option>
      </select>
      <span style="font-size:11.0pt;line-height:107%;
         font-family:&quot;Gill Sans&quot;,sans-serif;mso-fareast-font-family:Calibri;mso-fareast-theme-font:
         minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:EN-US;mso-bidi-language:
         AR-SA">Hole Punch</span> 
      <select name="holepunch">
         <option value="None">None</option>
         <option value="Punched">Punched</option>
      </select>
      Binding
   </span>
   <span style="font-size:11.0pt;line-height:107%;
      font-family:&quot;Gill Sans&quot;,sans-serif;mso-fareast-font-family:Calibri;mso-fareast-theme-font:
      minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:EN-US;mso-bidi-language:
      AR-SA">
      <select name="binding">
         <option value="None">None</option>
         <option value="Comb Binding">Comb Binding</option>
         <option value="Softback Binding">Softback Binding</option>
         <option value="Hardback Binding (without labels)">Hardback Binding (without labels)</option>
         <option value="Hardback binding (with labels)">Hardback binding (with labels)</option>
      </select>
      Lamination
      <select name="lamination">
         <option value="none">None</option>
         <option value="laminated">Laminated</option>
      </select>
   </span>
   <span style="font-size:11.0pt;line-height:107%;
      font-family:&quot;Gill Sans&quot;,sans-serif;mso-fareast-font-family:Calibri;mso-fareast-theme-font:
      minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:EN-US;mso-bidi-language:
      AR-SA">
      <span style="font-size:11.0pt">Collect from:</span> 
      <select name="collectfrom:" required="required">
         <option value="Guild Print Room (Forum level M1)">Guild Print Room (Forum level M1)</option>
         <option value="Guild Shop (Devonshire House)">Guild Shop (Devonshire House)</option>
         <option value="Guild Shop (St. Lukes)">Guild Shop (St. Lukes)</option>
      </select>
      <span style="font-size:11.0pt"> <small>If collecting from other than the Guild Print Room, payment will need to be made by debit/credit card over the telephone at no extra charge.</small></span> 
   </span>
</p>
<p><a href="https://www.exeterguild.org/pageassets/print/PR-Price-Booklet-161107.pdf"><small>View Prices (PDF)</small></a></p>

	

<p>
<label for='message'>Comments:</label> <br>
<textarea name="message"></textarea>


</p>
<p>
<label for='uploaded_file'>Select A File To Upload (25MB Max):</label> <br>
<input type="file" name="uploaded_file">
</p>
<input type="submit" value="Submit" name='submit'>
</form>
<script language="JavaScript">
// Code for validating the form
// Visit http://www.javascript-coder.com/html-form/javascript-form-validation.phtml
// for details
var frmvalidator  = new Validator("email_form_with_php");
frmvalidator.addValidation("name","req","Please provide your name"); 
frmvalidator.addValidation("email","req","Please provide your email"); 
frmvalidator.addValidation("email","email","Please enter a valid email address"); 
</script>
<noscript>
<small><a href='http://www.html-form-guide.com/email-form/php-email-form-attachment.html'
>How to attach file to email in PHP</a> article page.</small>
</noscript>

<?php echo "</div></div>"; ?>

</body>
</html>