<?php
/**
 * Extension should be applied to any viewable DataObject subclass
 * in SilverStripe. If applied to custom controllers (not extending from ContentController),
 * the {@link trackCount()} method needs to be invoked manually.
 */
class ViewCountableExtension extends DataExtension {

	public function updateCMSFields(FieldList $fields) {
		$fields->addFieldToTab(
			'Root.Main',
			ReadonlyField::create('ViewCount', 'View Counts', $this->ViewCount()->Count)
		);
	}
	
	/**
	 * @todo Should really be split into a separate controller extension,
	 * but SS doesn't have extension points for init() there...
	 */
	public function contentcontrollerInit() {
		$this->trackViewCount();
	}

	/**
	 * @return ViewCount
	 */
	public function trackViewCount() {
		// Don't track crawlers and bots
		$bots = Config::inst()->get('ViewCountableExtension', 'bots');
		foreach($bots as $bot) {
			if(stripos($bot, $_SERVER["HTTP_USER_AGENT"]) !== false) return;	
		}

		// Only track once per session
		$tracked = Session::get('ViewCountsTracked');
		if($tracked && array_key_exists($this->owner->ID, $tracked)) return;
		$tracked[$this->owner->ID] = true;
		Session::set('ViewCountsTracked', $tracked);

		// Track in DB. Not the most lightweight approach...
		$count = $this->ViewCount();
		if(!$count) {
			$count = new ViewCount(array(
				'RecordID' => $this->owner->ID,
				'RecordClass' => ClassInfo::baseDataClass($this->owner->ClassName),
			));
			$count->write();
		}
		$count->Count = $count->Count + 1;
		$count->write();

		return $count;
	}

	public function ViewCount() {
		return ViewCount::get()->filter(array(
			'RecordID' => $this->owner->ID,
			'RecordClass' => ClassInfo::baseDataClass($this->owner->ClassName)
		))->First();
	}

}