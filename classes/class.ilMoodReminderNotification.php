<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodReminderNotification
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodReminder
 */
class ilMoodReminderNotification extends ilMailNotification
{
	const SALUTATION_PLACEHOLDER_FIRSTNAME = '[FIRSTNAME]';
	const SALUTATION_PLACEHOLDER_LASTNAME = '[LASTNAME]';
	
	/**
	 * @var ilMoodReminderPlugin
	 */
	protected $plugin;
	
	public function __construct(ilMoodReminderPlugin $plugin)
	{
		parent::__construct(false);
		
		$this->plugin = $plugin;
	}
	
	public function send()
	{
		foreach($this->getRecipients() as $rcp)
		{
			$user = ilObjectFactory::getInstanceByObjId($rcp);
			
			if( !($user instanceof ilObjUser) )
			{
				continue;
			}
			
			$this->initMail();
			
			$this->setSubject($this->plugin->getConfig()->getReminderSubject());
			
			$this->appendBody($this->buildSalutation($user) . "\n\n");
			$this->appendBody($this->plugin->getConfig()->getReminderText());
			
			$this->sendMail(array($rcp), array('system'));
		}
	}
	
	public function buildSalutation(ilObjUser $user)
	{
		switch($user->getGender())
		{
			case 'm':
				
				$salutation = $this->plugin->getConfig()->getSalutationMale();
				break;
				
			case 'f':
				
				$salutation = $this->plugin->getConfig()->getSalutationFemale();
				break;
				
			case 'n':
			default:
			
				$salutation = $this->plugin->getConfig()->getSalutationNeutral();
				break;
		}
		
		foreach(self::getSalutationPlaceholders() as $placeholder)
		{
			switch($placeholder)
			{
				case self::SALUTATION_PLACEHOLDER_FIRSTNAME:
					
					$salutation = str_replace($placeholder, $user->getFirstname(), $salutation);
					break;
					
				case self::SALUTATION_PLACEHOLDER_LASTNAME:
					
					$salutation = str_replace($placeholder, $user->getLastname(), $salutation);
					break;
			}
		}
		
		return $salutation;
	}
	
	public static function getSalutationPlaceholders()
	{
		return array(
			self::SALUTATION_PLACEHOLDER_FIRSTNAME,
			self::SALUTATION_PLACEHOLDER_LASTNAME
		);
	}
}
