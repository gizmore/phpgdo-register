<?php
namespace GDO\Register\Method;

use GDO\Captcha\GDT_Captcha;
use GDO\Core\Application;
use GDO\Core\GDT_Hook;
use GDO\Core\GDO;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Mail\GDT_Email;
use GDO\Mail\Mail;
use GDO\Net\GDT_IP;
use GDO\Register\Module_Register;
use GDO\Register\GDO_UserActivation;
use GDO\Core\GDT;
use GDO\Core\GDT_Checkbox;
use GDO\Date\Time;
use GDO\Crypto\GDT_Password;
use GDO\User\GDT_Username;
use GDO\User\GDO_User;
use GDO\Crypto\BCrypt;
use GDO\Form\GDT_Validator;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Panel;
use GDO\Core\GDT_Response;
use GDO\UI\GDT_Message;
use GDO\Register\GDO_UserSignup;
use GDO\User\GDO_UserSetting;

/**
 * Registration form.
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.0
 */
class Form extends MethodForm
{
	public function isUserRequired() : bool { return false; }
	
	public function getUserType() : ?string { return 'ghost,guest'; }
	
	public function renderPage() : GDT
	{
	    if (Module_Register::instance()->cfgAdminActivation())
	    {
	        $response = GDT_Response::makeWith(GDT_Panel::make()->text('moderation_info'));
	        return $response->addField(parent::renderPage());
	    }
	    return parent::renderPage();
	}
	
	public function createForm(GDT_Form $form) : void
	{
		$module = Module_Register::instance();
		
		if ($module->cfgAdminActivationTest())
		{
		    $form->addField(GDT_Message::make('ua_message')->label('user_signup_text'));
		}
		
		$form->addField(GDT_Username::make('user_name')->required());
		$form->addField(GDT_Validator::make()->validatorFor($form, 'user_name', [$this, 'validateUniqueUsername']));
		$form->addField(GDT_Validator::make()->validatorFor($form, 'user_name', [$this, 'validateUniqueIP']));
		$form->addField(GDT_Password::make('user_password')->required());
		
		if ($module->cfgPasswordRetype())
		{
			$form->addField(GDT_Password::make('password_retype')->required()->label('password_retype'));
			$form->addField(GDT_Validator::make()->validatorFor($form, 'password_retype', [$this, 'validatePasswordRetype']));
		}
		if ($module->cfgEmailActivation() || $module->cfgAdminActivation())
		{
			$form->addField(GDT_Email::make('user_email')->required());
			$form->addField(GDT_Validator::make()->validatorFor($form, 'user_email', [$this, 'validateUniqueEmail']));
		}
		
// 		if (!Application::instance()->isCLI())
// 		{
    		if ($module->cfgTermsOfService())
    		{
    			$form->addField(GDT_Checkbox::make('tos')->required()->label('tos_label', [$module->cfgTosUrl(), $module->cfgPrivacyURL()]));
    			$form->addField(GDT_Validator::make()->validatorFor($form, 'tos', [$this, 'validateTOS']));
    		}
    		if ($module->cfgCaptcha())
    		{
    			$form->addField(GDT_Captcha::make('captcha'));
    		}
// 		}

		$form->addField(GDT_AntiCSRF::make());

		$form->actions()->addField(GDT_Submit::make()->label('btn_register'));
		
		GDT_Hook::callHook('RegisterForm', $form);
	}
	
	function validatePasswordRetype(GDT_Form $form, GDT $field)
	{
		if ($field->getVar() !== $form->getField('user_password')->getVar())
		{
			return $field->error('err_password_retype');
		}
		return true;
	}
	
	function validateUniqueIP(GDT_Form $form, GDT $field)
	{
		$ip = GDO::quoteS(GDT_IP::current());
		$cut = Application::$TIME - Module_Register::instance()->cfgMaxUsersPerIPTimeout();
		$cut = Time::getDate($cut);
		$count = GDO_UserSignup::table()->countWhere("us_ip={$ip} AND us_created>='{$cut}'");
		$max = Module_Register::instance()->cfgMaxUsersPerIP();
		return $count < $max ? true : $field->error('err_ip_signup_max_reached', [$max]);
	}
	
	public function validateUniqueUsername(GDT_Form $form, GDT_Username $username, $value)
	{
		$existing = GDO_User::table()->getByName($value);
		return $existing ? $username->error('err_username_taken') : true;
	}

	public function validateUniqueEmail(GDT_Form $form, GDT_Email $email, $value)
	{
		$count = GDO_UserSetting::table()->countWhere("uset_name='email' AND uset_var=".GDO::quoteS($email->getVar()));
		return $count == 0 ? true : $email->error('err_email_taken');
	}
	
	public function validateTOS(GDT_Form $form, GDT_Checkbox $field)
	{
		return $field->getValue() ? true : $field->error('err_tos_not_checked');
	}
	
	public function formInvalid(GDT_Form $form)
	{
		return $this->error('err_register');
	}
	
	public function formValidated(GDT_Form $form)
	{
		return $this->onRegister($form);
	}
	
	################
	### Register ###
	################
	public function onRegister(GDT_Form $form)
	{
		$module = Module_Register::instance();
		
		# TODO: GDT_Password should know it comes from form for a save...
		$password = $form->getField('user_password');
		$password->var(BCrypt::create($password->getVar())->__toString());
		
		$activation = GDO_UserActivation::table()->blank($form->getFormVars());
		$activation->setVar('user_register_ip', GDT_IP::current());
		GDT_Hook::callHook('OnRegister', $form, $activation);
		$activation->save();
		
		if ($module->cfgEmailActivation())
		{
			return $this->onEmailActivation($activation);
		}
		else
		{
			return Activate::make()->activate($activation->getID(), $activation->getToken());
		}
	}
	
	########################
	### Email Activation ###
	########################
	public function onEmailActivation(GDO_UserActivation $activation)
	{
		$module = Module_Register::instance();
		$mail = new Mail();
		$mail->setSubject(t('mail_activate_subj', [sitename()]));
		$mail->setBody($this->getMailBody($activation));
		$mail->setSender($module->cfgMailSender());
		$mail->setSenderName($module->cfgMailSenderName());
		$mail->setReceiver($activation->getEmail());
		$mail->sendAsHTML();
		return $this->message('msg_activation_mail_sent');
	}
	
	public function getMailBody(GDO_UserActivation $activation)
	{
		$tVars = [
			'username' => $activation->getUsername(),
			'activation_url' => $activation->getUrl(),
		];
		return GDT_Template::php('Register', 'mail/activate.php', $tVars);
	}
	
}
