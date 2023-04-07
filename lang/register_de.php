<?php
namespace GDO\Register\lang;

return [
	'module_register' => 'Registrierung',
	'cfg_captcha' => 'Registrierungs-Captcha aktivieren?',
	'cfg_guest_signup' => 'Gastkonten erlauben?',
	'cfg_email_activation' => 'Aktivierungs-Mail erzwingen?',
	'cfg_email_activation_timeout' => 'Zeit bis Aktivierung ausläuft',
	'cfg_admin_activation' => 'Admins müssen neue Mitglieder aktivieren?',
	'cfg_signup_ip' => 'Register-IP speichern?',
	'cfg_ip_signup_count' => 'Maximale Registrierungen per IP',
	'cfg_ip_signup_duration' => 'Verbleibende Zeit für Aktivierung',
	'cfg_force_tos' => 'Erzwinge das Lesen der AGB',
	'cfg_tos_url' => 'AGB Link URL',
	'cfg_privacy_url' => 'Datenschutzbestimmungen Link URL',
	'cfg_activation_login' => 'Automatisches Login nach Aktivierung',
	'cfg_signup_password_retype' => 'Erzwinge Passwort-Wiederholung',
	'cfg_signup_mail_sender' => 'Registrierungs-Mail Absender',
	'cfg_signup_mail_sender_name' => 'Registrierungs-Mail Absender Name',
	#############################################################################
	'btn_register' => 'Registrieren',
	'btn_guest' => 'Weiter als Gast',
	'mt_register_form' => 'Registrierung',
	'password_retype' => 'Erneut eingeben',
	'tos_label' => 'Ich habe die <a href="%s">Nutzungsbedingungen</a> und <a href="%s">Datenschutzbestimmungen</a> gelesen.',
	'msg_activation_mail_sent' => 'Wir haben Dir eine E-Mail gesendet. Folge dem Aktivierungslink dort um die registrierung abzuschliessen.',
	'msg_activated' => 'Willkommen, %s, Dein Konto ist nun aktiviert.',
	'msg_already_activated' => 'Ihr Konto war bereits aktiviert.',
	'err_username_taken' => 'Dieser Benutzername ist bereits vergeben.',
	'err_email_taken' => 'Diese E-Mail ist bereits vergeben.',
	'err_register' => 'Bei der Registrierung sind noch Fehler aufgetreten.',
	'err_no_activation' => 'Die aktivierung ist fehlgeschlagen.',
	'err_ip_signup_max_reached' => 'Es wurden bereits %s Konten mit Ihrer IP erstellt. Bitte wartet etwas.',
	'err_tos_not_checked' => 'Sie müssen den Nutzungsbedingungen zustimmen um fortzufahren.',
	'err_password_retype' => 'Sie müssen zweimal das gleiche Passwort eingeben.',
	#############################################################################
	'mt_register_guest' => 'Als Gast fortfahren.',
	'btn_signup_guest' => 'Nickname verwenden',
	'err_guest_name_taken' => 'Dieser Nickname ist bereits in Benutzung',
	'msg_registered_as_guest' => 'Du bist nun als %s angemeldet.',
	'msg_registered_as_guest_back' => 'Du bist nun als %s angemeldet. <a href="%s">Klicke hier</a> um fortzufahren.',
	'link_register' => 'zur Registrierung',
	'link_register_guest' => 'weiter als Gast',
	#############################################################################
	'mail_activate_subj' => '[%s] Activieren Sie Ihr Konto',
	#############################################################################
	'mt_register_form' => 'Registrieren',
	'mt_register_guest' => 'Als Gast anmelden',
	'mt_register_tos' => 'Nutzungsbedingungen',
	#############################################################################
	'moderation_info' => 'Die Registrierung erfordert zur Zeit die aktivierung durch einen Administrator. Wir bitten um Geduld.',
	'msg_registration_confirmed_but_moderation' => 'Sie haben Ihre E-Mail bestätigt and warten nun auf eine Aktivierung durch einen Administrator.',
	'mail_moderate_subj' => '%s: Neue Anmeldung',
	'mail_activate2_subj' => '%s: Konto aktiviert',
	'user_signup_text' => 'Bitte schreiben Sie etwas über sich',
	'cfg_admin_activation_test' => 'Sollen die Nutzer erst etwas über sich schreiben?',
	'link_activations' => 'Wartende Aktivierungen',
	'ua_email_confirmed' => 'E-Mail bestätigt',
	'msg_already_confirmed' => 'Ihre E-Mail wurde bereits bestätigt. Sie müssen nun auf einen Administrator warten, um Ihr Konto zu aktivieren.',
	'btn_activate' => 'Aktivieren',
	'msg_user_activated' => 'Der Nutzer %s wurde erfolgreich aktiviert.',
	'list_register_activations' => '%s wartende Aktivierungen',
	#############################################################################
	'msg_admin_will_activate_you' => 'Eine Email wurde an die Administratoren gesendet, mit einer Kopie für Sie. Sobald Sie aktiviert wurden erhalten Sie eine weiter Email.',
	'register_date' => 'Registriert am',
	'mt_register_activations' => 'Wartende Aktivierungen',
	'activation_speed' => 'Aktivierungsdauer',
];
