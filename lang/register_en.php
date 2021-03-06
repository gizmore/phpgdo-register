<?php
namespace GDO\Register\lang;
return [
	'cfg_captcha' => 'Force captcha for sign-up',
	'cfg_guest_signup' => 'Allow guest sign-up',
	'cfg_email_activation' => 'Force member activation by email',
	'cfg_email_activation_timeout' => 'Timeout for email activation',
	'cfg_admin_activation' => 'Force admin activation',
	'cfg_ip_signup_count' => 'Max number of accounts per IP',
	'cfg_ip_signup_duration' => 'IP reject timeout',
	'cfg_force_tos' => 'Force TOS reading',
	'cfg_tos_url' => 'ToS link URL',
	'cfg_privacy_url' => 'Privacy agreement link URL',
	'cfg_activation_login' => 'Login automatically after activation',
	'cfg_signup_password_retype' => 'Force password retype upon sign-up',
	'cfg_signup_mail_sender' => 'Registration mail sender',
	'cfg_signup_mail_sender_name' => 'Registration mail sender name',
	#############################################################################
	'btn_register' => 'Sign up',
	'btn_guest' => 'As Guest',
	'ft_register_form' => 'Sign up',
	'password_retype' => 'Retype Password',
	'tos_label' => 'I have read and agree to the <a href="%s">Terms of Service</a> and the <a href="%s">privacy implications</a>.',
	'msg_activation_mail_sent' => 'We have sent you an email. To complete your sign up process please follow the instructions there.',
	'msg_activated' => 'Welcome, %s, your account is now activated.',
	'msg_already_activated' => 'Your account was already activated.',
	'err_username_taken' => 'This username is already in use.',
	'err_email_taken' => 'This email is already resgistered here.',
	'err_register' => 'The sign up process failed.',
	'err_no_activation' => 'Activating your account failed.',
	'err_ip_signup_max_reached' => 'There are already %s accounts with your IP registered lately.',
	'err_tos_not_checked' => 'You have to accept the terms of service to sign up.',
	'err_password_retype' => 'You have to retype your password correctly.',
	#############################################################################
	'ft_register_guest' => 'Continue as Guest',
	'btn_signup_guest' => 'Use nickname',
	'err_guest_name_taken' => 'This guest name has been taken already.',
	'msg_registered_as_guest' => 'You are now using the site as %s.',
	'msg_registered_as_guest_back' => 'You are now using the site as %s. <a href="%s">Click here</a> to continue.',
	'link_register' => 'goto Signup',
	'link_register_guest' => 'continue as Guest',
	#############################################################################
	'mail_activate_subj' => '%s: Activate your account',
	#############################################################################
	'mdescr_register_form' => 'Register',
	'mdescr_register_guest' => 'Register as guest',
	'mdescr_register_tos' => 'Terms of service',
	#############################################################################
	'moderation_info' => 'The signup process is currently requiering an adminstrator to unlock accounts. Please be patient.',
	'msg_registration_confirmed_but_moderation' => 'You have confirmed your email and now have to wait until an administrator activates you.',
	'mail_moderate_subj' => '%s: New Registration',
	'mail_activate2_subj' => '%s: Account activated',
	'user_signup_text' => 'Please write something about you',
	'cfg_admin_activation_test' => 'Shall the user write something about himself?',
	'link_activations' => 'Pending activations',
	'ua_email_confirmed' => 'Email confirmed',
	'msg_already_confirmed' => 'Your Email has been confirmed already. You have to wait for an administrator to activate your account.',
	'btn_activate' => 'Activate',
	'msg_user_activated' => 'The user %s has been activated successfully.',
	'list_register_activations' => '%s pending activations',
	#################################################################
	'msg_admin_will_activate_you' => 'An email has been sent tothe administrators with a copy for you. As soon as you have been activated, you will receive another email.',
];
