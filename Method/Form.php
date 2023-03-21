<?php
namespace GDO\Register\Method;

use GDO\Captcha\GDT_Captcha;
use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDO_Error;
use GDO\Core\GDT;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Hook;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Template;
use GDO\Core\GDT_Tuple;
use GDO\Crypto\BCrypt;
use GDO\Crypto\GDT_Password;
use GDO\Date\Time;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_Validator;
use GDO\Form\MethodForm;
use GDO\Mail\GDT_Email;
use GDO\Mail\Mail;
use GDO\Net\GDT_IP;
use GDO\Register\GDO_UserActivation;
use GDO\Register\GDO_UserSignup;
use GDO\Register\Module_Register;
use GDO\UI\GDT_Message;
use GDO\UI\GDT_Panel;
use GDO\User\GDO_User;
use GDO\User\GDO_UserSetting;
use GDO\User\GDT_Username;

/**
 * Registration form.
 *
 * @version 7.0.1
 * @since 3.0.0
 * @author gizmore
 */
class Form extends MethodForm
{

	public function isUserRequired(): bool { return false; }

	public function getUserType(): ?string { return 'ghost,guest'; }

	public function validatePasswordRetype(GDT_Form $form, GDT $field)
	{
		if ($field->getVar() !== $form->getField('user_password')->getVar())
		{
			return $field->error('err_password_retype');
		}
		return true;
	}

	public function validateUniqueIP(GDT_Form $form, GDT $field)
	{
		$ip = GDO::quoteS(GDT_IP::current());
		$cut = Application::$TIME - Module_Register::instance()->cfgMaxUsersPerIPTimeout();
		$cut = Time::getDate($cut);
		$count = GDO_UserSignup::table()->countWhere("us_ip={$ip} AND us_created>='{$cut}'");
		$max = Module_Register::instance()->cfgMaxUsersPerIP();
		return ($count < $max) ||
			$field->error('err_ip_signup_max_reached', [$max]);
	}

	public function validateUniqueUsername(GDT_Form $form, GDT_Username $username, $value): bool
	{
		$existing = GDO_User::table()->getByName($value);
		return $existing || $username->error('err_username_taken');
	}	public function renderPage(): GDT
	{
		if (Module_Register::instance()->cfgAdminActivation())
		{
			$response = GDT_Response::makeWith(GDT_Panel::make()->text('moderation_info'));
			return $response->addField(parent::renderPage());
		}
		return parent::renderPage();
	}

	public function validateUniqueEmail(GDT_Form $form, GDT_Email $email, $value): bool
	{
		$count = GDO_UserSetting::table()->countWhere("uset_name='email' AND uset_var=" . GDO::quoteS($email->getVar()));
		return ($count == 0) || $email->error('err_email_taken');
	}

	public function validateTOS(GDT_Form $form, GDT_Checkbox $field)
	{
		return $field->getValue() || $field->error('err_tos_not_checked');
	}

	public function createForm(GDT_Form $form): void
	{
		$module = Module_Register::instance();

		if ($module->cfgAdminActivationTest())
		{
			$form->addField(GDT_Message::make('ua_message')->label('user_signup_text'));
		}

		$form->addField(GDT_Username::make('user_name')->notNull());
		$form->addField(GDT_Validator::make('valid_signup_name')->validatorFor($form, 'user_name', [$this, 'validateUniqueUsername']));
		$form->addField(GDT_Validator::make('valid_signup_ip')->validatorFor($form, 'user_name', [$this, 'validateUniqueIP']));
		$form->addField(GDT_Password::make('user_password')->notNull());

		if ($module->cfgPasswordRetype())
		{
			$form->addField(GDT_Password::make('password_retype')->notNull()->label('password_retype'));
			$form->addField(GDT_Validator::make('valid_password_retype')->validatorFor($form, 'password_retype', [$this, 'validatePasswordRetype']));
		}
		if ($module->cfgEmailActivation() || $module->cfgAdminActivation())
		{
			$form->addField(GDT_Email::make('user_email')->notNull());
			$form->addField(GDT_Validator::make('valid_user_email')->validatorFor($form, 'user_email', [$this, 'validateUniqueEmail']));
		}

// 		if (!Application::instance()->isCLI())
// 		{
		if ($module->cfgTermsOfService())
		{
			$form->addField(GDT_Checkbox::make('tos')->notNull()->label('tos_label', [$module->cfgTosUrl(), $module->cfgPrivacyURL()]));
			$form->addField(GDT_Validator::make('valid_tos')->validatorFor($form, 'tos', [$this, 'validateTOS']));
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




	public function formInvalid(GDT_Form $form)
	{
		return GDT_Tuple::makeWith(
			$this->error('err_register'),
			$this->renderPage());
	}

	public function formValidated(GDT_Form $form)
	{
		return $this->onRegister($form);
	}

	################
	### Register ###
	################
	/**
	 * @throws GDO_Error
	 */
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
	public function onEmailActivation(GDO_UserActivation $activation): GDT
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

	public function getMailBody(GDO_UserActivation $activation): string
	{
		$tVars = [
			'username' => $activation->getUsername(),
			'activation_url' => $activation->getUrl(),
		];
		return GDT_Template::php('Register', 'mail/activate.php', $tVars);
	}

}
