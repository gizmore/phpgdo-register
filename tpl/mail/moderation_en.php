<?php
namespace GDO\Register\tpl\mail;
/** @var $nick string * */
/** @var $username string * */
/** @var $email string * */
/** @var $ip string * */
/** @var $message string * */
?>
<div>
    Hello <?=$username?><br/>
    <br/>
    A new user has registered on <?=sitename()?><br/>
    <br/>
    <pre>
Nickname: <?=html($nick)?>
Email: <?=html($email)?>
IP: <?=html($ip)?>

Message:

<?=$message?>

</pre>
    <br/>
    You can now unlock him in the activations page.<br/>
    <br/>
    <br/>
    Kind Regards<br/>
    The <?=sitename()?> Team<br/>
</div>
