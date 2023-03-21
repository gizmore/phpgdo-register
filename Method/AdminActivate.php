<?php
namespace GDO\Register\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDT_Template;
use GDO\Core\Method;
use GDO\Language\Trans;
use GDO\Mail\Mail;
use GDO\Register\GDO_UserActivation;
use GDO\Register\Module_Register;
use GDO\User\GDO_User;
use GDO\Util\Common;

final class AdminActivate extends Method
{

	use MethodAdmin;

	public function isTrivial(): bool { return false; }

	public function execute()
	{
		$activation = GDO_UserActivation::table()->find(Common::getRequestString('id'));

		if ($activation->isDeleted())
		{
			return $this->message('msg_already_activated');
		}

		# Activate
		$user = Activate::make()->activateToken($activation);

		$this->sendMail($user);

		return $this->message('msg_user_activated', [$user->renderUserName()]);
	}

	private function sendMail(GDO_User $user)
	{
		$module = Module_Register::instance();
		$mail = new Mail();
		$mail->setSubject(tusr($user, 'mail_activate2_subj', [sitename()]));
		$body = $this->getMailBody($user);
		$mail->setBody($body);
		$mail->setSender($module->cfgMailSender());
		$mail->setSenderName($module->cfgMailSenderName());
		$mail->sendToUser($user);
	}

	public function getMailBody(GDO_User $user)
	{
		$tVars = [
			'username' => $user->renderUserName(),
			'admin' => GDO_User::current()->renderUserName(),
			'url' => url(GDO_MODULE, GDO_METHOD),
		];
		$old = Trans::$ISO;
		Trans::setISO($user->getLangISO());
		$body = GDT_Template::php('Register', 'mail/activate2.php', $tVars);
		Trans::setISO($old);
		return $body;
	}

	public function getPermission(): ?string { return 'staff'; }

	public function onRenderTabs(): void
	{
		$this->renderAdminBar();
		Module_Register::instance()->renderAdminBar();
	}

}
