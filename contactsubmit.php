<?php
/*
    ********************************************************************************************
    INITIALISATION
    ********************************************************************************************
*/
$template = 'contact';
$destination = 'duschewen.contact@gmail.com';
$showonwin="";
$showonfail="";
$clone = 'no'; // 'yes' or 'no'

$message_envoye = "I got your message !";
$message_non_envoye = "Nothing has been sent, can you please try again.";

$message_erreur_formulaire = "You have to fill the form.";
$message_formulaire_invalide = "Check if all fields are set, and if you didnt make any errors.";

if (!isset($_POST['send'])) :
    // fail send
   $showonfail = $message_erreur_formulaire;
include 'layout.phtml';

else :

    /*
     * this function is set to clean and save a text
     */
    function Rec($text)
    {
        $text = htmlspecialchars(trim($text), ENT_QUOTES);
        if (1 === get_magic_quotes_gpc())
        {
            $text = stripslashes($text);
        }
 
        $text = nl2br($text);
        return $text;
    };
 
    /*
     * This function check the synthax of an email
     */
    function IsEmail($email)
    {
        $value = preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
        return (($value === 0) || ($value === false)) ? false : true;
    }
 
    // form is sent , let's check the fields
    $name    = (isset($_POST['name']))     ? Rec($_POST['name'])     : '';
    $email   = (isset($_POST['email']))   ? Rec($_POST['email'])   : '';
    $object   = (isset($_POST['presta']))   ? Rec($_POST['presta'])   : '';
    $message = (isset($_POST['text'])) ? Rec($_POST['text']) : '';
 
    // Let's check the email var
    $email = (IsEmail($email)) ? $email : ''; // if it is empty or false throw it  , if not it take the value
 
    if (($name != '') && ($email != '') && ($object != '') && ($message != ''))
    {
        // the 4 var are set , we can creat and send the email
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'From:'.$name.' <'.$email.'>' . "\r\n" .
                'Reply-To:'.$email. "\r\n" .
                'Content-Type: text/plain; charset="utf-8"; DelSp="Yes"; format=flowed '."\r\n" .
                'Content-Disposition: inline'. "\r\n" .
                'Content-Transfer-Encoding: 7bit'." \r\n" .
                'X-Mailer:PHP/'.phpversion();
    
        // should we send a clone ?
        if ($clone == 'yes')
        {
            $target = $destination.';'.$email;
        }
        else
        {
            $target = $destination;
        };
 
        // Replacing some special chars
        $message = str_replace("&#039;","'",$message);
        $message = str_replace("&#8217;","'",$message);
        $message = str_replace("&quot;",'"',$message);
        $message = str_replace('<br>','',$message);
        $message = str_replace('<br />','',$message);
        $message = str_replace("&lt;","<",$message);
        $message = str_replace("&gt;",">",$message);
        $message = str_replace("&amp;","&",$message);
 
        // Sending the email
        $num_emails = 0;
        $tmp = explode(';', $target);
        foreach($tmp as $email_destination)
        {
            if (mail($email_destination, $object, $message, $headers))
                $num_emails++;
        }
 
        if ((($clone == 'yes') && ($num_emails == 2)) || (($clone == 'no') && ($num_emails == 1)))
        {
            $showonwin = $message_envoye;
        }
        else
        {
            $showonfail = $message_non_envoye;
        };
    }
    else
    {
        // one or more than 3 var are empty
        $showonfail = $message_formulaire_invalide;
    };
    include 'layout.phtml';

endif; // end of if (!isset($_POST['envoi']))






































































/*
if (!empty($_POST)) :	
$mail = 'duschewen.contact@gmail.com';
        // Déclaration de l'adresse de destination.
        if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui rencontrent des bogues.
        {
            $passage_ligne = "\r\n";
        }
        else
        {
            $passage_ligne = "\n";
        }
        //=====Déclaration des messages au format texte et au format HTML.
        $message_txt = "Message de  " . $_POST['firstname'].' '.$_POST['lastname']. $passage_ligne . $passage_ligne .
       
        "Message partie php" . $_POST['text']  . $passage_ligne .
        "Prestation : " . $_POST['presta'] . $passage_ligne .
        "email " . $_POST['email']  . $passage_ligne . $passage_ligne .
        
        
        $message_html = "<html><head></head><body>Message de " . $_POST['firstname'].''.$_POST['lastname']."<br /><br /> 
        <strong>Message : </strong> " . $_POST['text'] .  "<br /> 
       
        <strong>email : </strong>" . $_POST['email'] . "<br />
        
         </body></html>";
        //==========
        
        //=====Création de la boundary
        $boundary = "-----=".md5(rand());
        //==========
         
        //=====Définition du sujet.
        $sujet = "Message laisser sur le site duschewen.com";
        //=========
         
        //=====Création du header de l'e-mail.
        $header = "From: \"PageContactDuschEwen\"<duschewen.contact@gmail.com>".$passage_ligne;
        $header.= "Reply-to: \"PageContactDuschEwen\" <duschewen.contact@gmail.com>".$passage_ligne;
        $header .= "Bcc: <duschewen.contact@gmail.com>" .$passage_ligne;
        $header.= "MIME-Version: 1.0".$passage_ligne;
        $header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
        //==========
         
        //=====Création du message.
        $message = $passage_ligne."--".$boundary.$passage_ligne;
        //=====Ajout du message au format texte.
        $message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
        $message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
        $message.= $passage_ligne.$message_txt.$passage_ligne;
        //==========
        $message.= $passage_ligne."--".$boundary.$passage_ligne;
        //=====Ajout du message au format HTML
        $message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
        $message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
        $message.= $passage_ligne.$message_html.$passage_ligne;
        //==========
        $message.= $passage_ligne."--".$boundary."--".$passage_ligne;
        $message.= $passage_ligne."--".$boundary."--".$passage_ligne;
        //==========   
        //=====Envoi de l'e-mail.
        mail($mail,$sujet,$message,$header);
       
endif;*/