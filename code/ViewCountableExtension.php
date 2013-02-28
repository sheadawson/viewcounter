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

		// Don't track draft views
		if($this->owner->hasExtension('Versioned') && Versioned::current_stage() != "Live") return;

		// Only track once per session
		$tracked = Session::get('ViewCountsTracked');
		if($tracked && array_key_exists($this->owner->ID, $tracked)) return;
		$tracked[$this->owner->ID] = true;
		Session::set('ViewCountsTracked', $tracked);

		// Track in DB
		DB::query(sprintf(
			'INSERT INTO "ViewCount" ("Count", "RecordID", "RecordClass") '
			. 'VALUES (1, %d, \'%s\') ON DUPLICATE KEY UPDATE "Count"="Count"+1',
			$this->owner->ID,
			ClassInfo::baseDataClass($this->owner->ClassName)
		));
	}

	public function ViewCount() {
		return ViewCount::get()->filter(array(
			'RecordID' => $this->owner->ID,
			'RecordClass' => ClassInfo::baseDataClass($this->owner->ClassName)
		))->First();
	}

}