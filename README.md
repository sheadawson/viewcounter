# Simple View Counts for SilverStripe records

[![Build Status](https://secure.travis-ci.org/chillu/viewcounter.png)](http://travis-ci.org/chillu/viewcounter)

Tracks page views in a simple counter record, and the behaviour is applied
to any `DataObject` subclass through an extension.
This approach of a separate counter record is particularly handy
if you apply it to `Versioned` objects like `SiteTree`, since
you can store the view data independently of staging and live concerns.

Views are limited by session. This is a slightly simplistic approach, 
since it doesn't use other criteria such
as the current IP address or browser cookies to prevent abuse.
Common web crawlers and search engine bots are excluded from view counts.

## Usage

Add the following to your YAML config (e.g. `mysite/_config/config.yml`):

	MyRecordClass:
		extensions:
			- ViewCountableExtension

Views stored in a `ViewCount` record which relates to your record class.
If applying the extension to a `SiteTree` subclass, views are automatically tracked.
For other classes, call `trackViewCount()` in your own controller `init()` method.