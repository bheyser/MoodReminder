<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilCronMoodReminderPlugin
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodReminder
 */
class ilMoodReminderPlugin extends ilCronHookPlugin
{
	const MOOD_BAROMETER_COMP_TYPE = IL_COMP_SERVICE;
	const MOOD_BAROMETER_COMP_NAME = 'UIComponent';
	const MOOD_BAROMETER_PSLOT_ID = 'uihk';
	const MOOD_BAROMETER_PLUGIN_ID = 'moodbar';
	const MOOD_BAROMETER_PLUGIN_NAME = 'MoodBarometer';
	
	/**
	 * @var ilMoodReminderConfig
	 */
	protected $config;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->includePluginClasses();
		
		$this->config = new ilMoodReminderConfig($this->getSlotId().'_'.$this->getId());
	}
	
	/**
	 * @return ilMoodReminderConfig
	 */
	public function getConfig()
	{
		return $this->config;
	}
	
	public function includePluginClasses()
	{
		$this->includeClass('class.ilMoodReminderCron.php');
		$this->includeClass('class.ilMoodReminderConfig.php');
		$this->includeClass('class.ilMoodReminderNotification.php');
	}
	
	public function getPluginName()
	{
		return "MoodReminder";
	}
	
	public function getCronJobInstances()
	{
		return array($this->buildCronJobInstance());
	}
	
	public function getCronJobInstance($a_job_id)
	{
		return $this->buildCronJobInstance();
	}
	
	private function buildCronJobInstance()
	{
		$cron = new ilMoodReminderCron();
		$cron->setPlugin($this);
		return $cron;
	}
	
	/**
	 * @return bool
	 */
	public function checkMoodBarometerPluginAvailable()
	{
		return ilPluginAdmin::isPluginActive(self::MOOD_BAROMETER_PLUGIN_ID);
	}
	
	/**
	 * @return ilMoodBarometerPlugin
	 */
	public function getMoodBarometerPlugin()
	{
		/* @var ilMoodBarometerPlugin $moodBarometerPlugin */
		
		$moodBarometerPlugin = ilPluginAdmin::getPluginObject(
			self::MOOD_BAROMETER_COMP_TYPE, self::MOOD_BAROMETER_COMP_NAME,
			self::MOOD_BAROMETER_PSLOT_ID, self::MOOD_BAROMETER_PLUGIN_NAME
		);
		
		return $moodBarometerPlugin;
	}
}
