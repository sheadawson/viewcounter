# Simple View Counts for SilverStripe records

Tracks page views in a simple counter record, with a limit of one view per session.
Very simplistic approach, since it doesn't use other criteria such
as the current IP address or browser cookies to prevent abuse.

## Usage

Add the following to your YAML config (e.g. `mysite/_config/config.yml`):

	MyRecordClass:
		extensions:
			- ViewCountableExtension

Views stored in a `ViewCount` record which relates to your record class.
If applying the extension to a `SiteTree` subclass, views are automatically tracked.
For other classes, call `trackViewCount()` in your own controller `init()` method.