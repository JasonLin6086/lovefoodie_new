<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Snowfire\Beautymail\Beautymail;

class BeautyMailable extends Mailable
{

	/**
	 * Send the message using the given mailer.
	 *
	 * @param  \Illuminate\Contracts\Mail\Mailer  $mailer
	 * @return void
	 */
	public function send(MailerContract $mailer)
	{
		Container::getInstance()->call([$this, 'build']);

		$beautymail = app()->make(Beautymail::class);

		$beautymail->send($this->buildView(), $this->buildViewData(), function ($message) {
			$this->buildFrom($message)
				->buildRecipients($message)
				->buildSubject($message)
				->buildAttachments($message)
				->runCallbacks($message);
		});
	}
}