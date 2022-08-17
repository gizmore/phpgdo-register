<?php
namespace GDO\Register;

use GDO\Core\Application;
use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_UInt;
use GDO\Date\GDT_Duration;
use GDO\Net\GDT_Url;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\Form\GDT_Form;
use GDO\UI\GDT_Button;
use GDO\Mail\GDT_Email;
use GDO\Net\GDT_IP;
use GDO\Date\GDT_DateTime;
use GDO\Date\Time;
use GDO\User\Module_User;
use GDO\Core\GDT_String;

/**
 * Registration module.
 * 
 * Users that await activation are stored in a separate table, GDO_UserActivation.
 * This way, usernames or emails don't get burned.
 * 
 * This module features Guest Signup.
 * This module features Email Activation.
 * This module features Instant Activation.
 * This module features Moderation Activation.
 * This module features Admin Signup Moderation Activation.
 * This module features Terms of Service and Privacy pages.
 * This module features TellUsAboutYou moderation. It is copied to about_me automagically.
 *
 * @TODO Guest to Member conversion.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.0
 * 
 * @see Module_ActivationAlert
 * @see GDO_UserActivation
 */
class Module_Register extends GDO_Module
{
    public int $priority = 40;
    
	##############
	### Module ###
	##############
	public function getFriendencies() : array { return ['Cronjob', 'Mail']; }
	public function onLoadLanguage() : void { $this->loadLanguage('lang/register'); }
	public function href_administrate_module() : ?string { return href('Register', 'Admin'); }

	public function getClasses() : array
	{
		return [
			GDO_UserActivation::class,
			GDO_UserSignup::class,
		];
	}

	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
			GDT_Checkbox::make('captcha')->initial('1'),
			GDT_Checkbox::make('guest_signup')->initial('1'),
			GDT_Checkbox::make('email_activation')->initial('1'),
			GDT_Duration::make('email_activation_timeout')->initial("2h")->min(0)->max(31536000),
		    GDT_Checkbox::make('admin_activation')->initial('0'),
		    GDT_Checkbox::make('admin_activation_test')->initial('0'),
		    GDT_UInt::make('ip_signup_count')->initial('4')->min(0)->max(100),
		    GDT_UInt::make('local_ip_signup_count')->initial('100000')->min(0)->max(100000),
		    GDT_Duration::make('ip_signup_duration')->initial('24h')->min(0)->max(31536000),
			GDT_Checkbox::make('force_tos')->initial('1'),
			GDT_Url::make('tos_url')->allowAll(true)->initial(href('Register', 'TOS', '', false)),
			GDT_Url::make('privacy_url')->allowAll(true)->initial(href('Core', 'Privacy', '', false)),
			GDT_Checkbox::make('activation_login')->initial('1'),
			GDT_Checkbox::make('signup_password_retype')->initial('0'),
			GDT_Email::make('signup_mail_sender')->initial(GDO_BOT_EMAIL),
			GDT_String::make('signup_mail_sender_name')->icon('email')->initial(GDO_BOT_NAME),
		    GDT_Checkbox::make('hook_sidebar')->initial('1'),
		];
	}
	public function cfgCaptcha() { return module_enabled('Captcha') && $this->getConfigValue('captcha'); }
	public function cfgGuestSignup() { return $this->getConfigValue('guest_signup'); }
	public function cfgEmailActivation() { return $this->getConfigValue('email_activation'); }
	public function cfgEmailActivationTimeout() { return $this->getConfigValue('email_activation_timeout'); }
	public function cfgAdminActivation() { return $this->getConfigValue('admin_activation'); }
	public function cfgAdminActivationTest() { return $this->getConfigValue('admin_activation_test'); }
	public function cfgMaxUsersPerIP()
	{
	    return GDT_IP::isLocal() ? 
	        $this->getConfigValue('local_ip_signup_count') :
	        $this->getConfigValue('ip_signup_count');
	}
	public function cfgMaxUsersPerIPTimeout() { return $this->getConfigValue('ip_signup_duration'); }
	public function cfgTermsOfService() { return $this->getConfigValue('force_tos'); }
	public function cfgTosUrl() { return $this->getConfigVar('tos_url'); }
	public function cfgPrivacyUrl() { return $this->getConfigVar('privacy_url'); }
	public function cfgActivationLogin() { return $this->getConfigValue('activation_login'); }
	public function cfgPasswordRetype() { return $this->getConfigValue('signup_password_retype'); }
	public function cfgMailSender() { return $this->getConfigVar('signup_mail_sender'); }
	public function cfgMailSenderName() { return $this->getConfigVar('signup_mail_sender_name'); }
	public function cfgRightBar() { return $this->getConfigValue('hook_sidebar'); }
	
	############
	### Init ###
	############
	public function onInitSidebar() : void
	{
	    if ($this->cfgRightBar())
	    {
    		if (!GDO_User::current()->isUser())
    		{
    	        $navbar = GDT_Page::$INSTANCE->rightBar();
    			$navbar->addField(GDT_Link::make('btn_register')->href(href('Register', 'Form')));
    		}
	    }
	}

	##################
	### Admin tabs ###
	##################
	public function renderAdminBar()
	{
	    if (Application::instance()->isHTML())
	    {
    	    $tabs = GDT_Bar::make()->horizontal();
    	    $tabs->addField(GDT_Link::make('link_activations')->href(href('Register', 'Activations')));
    	    GDT_Page::$INSTANCE->topResponse()->addField($tabs);
	    }
	}
	
	##################
	### Form Hooks ###
	##################
	public function hookLoginForm(GDT_Form $form)
	{
	    $form->actions()->addField(GDT_Button::make('link_register')->secondary()->href(href('Register', 'Form')));
		if ($this->cfgGuestSignup())
		{
			$form->actions()->addField(GDT_Button::make('link_register_guest')->secondary()->href(href('Register', 'Guest')));
		}
	}
	
	public function hookRegisterForm(GDT_Form $form)
	{
		if ($this->cfgGuestSignup())
		{
			$form->actions()->addField(GDT_Button::make('link_register_guest')->secondary()->href(href('Register', 'Guest')));
		}
	}
	
	public function hookGuestForm(GDT_Form $form)
	{
		$form->actions()->addField(GDT_Button::make('link_register')->secondary()->href(href('Register', 'Form')));
	}
	
	public function hookUserActivated(GDO_User $user, GDO_UserActivation $activation=null)
	{
		$this->saveUserSetting($user, 'register_ip', GDT_IP::$CURRENT);
		$this->saveUserSetting($user, 'register_date', Time::getDate());
		if ($activation)
		{
			$aboutMe = $activation->getMessage();
			Module_User::instance()->saveUserSetting($user, 'about_me', $aboutMe);
		}
		GDO_UserSignup::onSignup($user);
	}
	
	################
	### Settings ###
	################
	public function getUserConfig() : array
	{
		return [
			GDT_IP::make('register_ip')->noacl(),
			GDT_DateTime::make('register_date'),
		];
	}
	
}
