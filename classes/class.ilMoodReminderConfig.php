<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodBarometerConfig
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodReminder
 */
class ilMoodReminderConfig
{
	/**
	 * @var ilSetting
	 */
	protected $settings;
	
	/**
	 * ilMoodBarometerConfig constructor.
	 * @param string $settingsId
	 */
	public function __construct($settingsId)
	{
		$this->settings = new ilSetting($settingsId);
	}
	
	public function getSalutationNeutral()
	{
		return $this->settings->get('salutation_n', '');
	}
	
	public function setSalutationNeutral($salutationNeutral)
	{
		$this->settings->set('salutation_n', $salutationNeutral);
	}
	
	public function getSalutationFemale()
	{
		return $this->settings->get('salutation_n', '');
	}
	
	public function setSalutationFemale($salutationFemale)
	{
		$this->settings->set('salutation_f', $salutationFemale);
	}
	
	public function getSalutationMale()
	{
		return $this->settings->get('salutation_m', '');
	}
	
	public function setSalutationMale($salutationMale)
	{
		$this->settings->set('salutation_m', $salutationMale);
	}
	
	public function getReminderSubject()
	{
		return $this->settings->get('reminder_subject', '');
	}
	
	public function setReminderSubject($reminderSubject)
	{
		$this->settings->set('reminder_subject', $reminderSubject);
	}
	
	public function getReminderText()
	{
		return $this->settings->get('reminder_text', '');
	}
	
	public function setReminderText($reminderText)
	{
		$this->settings->set('reminder_text', $reminderText);
	}
}
