<?php
class ViewCountableExtensionTest extends FunctionalTest
{

    protected $usesDatabase = true;

    public static $fixture_file = 'ViewCountableExtensionTest.yml';

    protected $requiredExtensions = array(
        'Page' => array(
            'ViewCountableExtension'
        ),
    );

    public function setUp()
    {
        parent::setUp();

        $page1 = $this->objFromFixture('Page', 'page1');
        $page1->publish('Stage', 'Live');
        $page2 = $this->objFromFixture('Page', 'page2');
        $page2->publish('Stage', 'Live');

        Versioned::reading_stage('Live');
    }

    public function testViewCountTracksOncePerSession()
    {
        $page1 = $this->objFromFixture('Page', 'page1');
        $page2 = $this->objFromFixture('Page', 'page2');

        $response = $this->get($page1->RelativeLink());
        $this->assertFalse($response->isError());
        $response = $this->get($page1->RelativeLink());
        $this->assertFalse($response->isError());
        $page1 = Page::get()->byID($page1->ID);
        $page2 = Page::get()->byID($page2->ID);
        $this->assertEquals(1, $page1->ViewCount()->Count, 'Doesnt double track');
        $this->assertEquals(0, $page2->ViewCount()->Count, 'Doesnt track other pages');

        // TODO Fix 404s
        // $response = $this->get($page2->RelativeLink());
        // $this->session()->inst_clearAll();
        // $response = $this->get($page2->RelativeLink());
        // $this->session()->inst_clearAll();
        // $page2 = Page::get()->byID($page2->ID);
        // $this->assertEquals(2, $page2->ViewCount()->Count, 'Tracks for individual sessions');
    }

    public function testExcludesBots()
    {
        $page1 = $this->objFromFixture('Page', 'page1');
        $response = $this->get($page1->RelativeLink());
        $this->assertFalse($response->isError());
        $page1 = Page::get()->byID($page1->ID);
        $this->assertEquals(1, $page1->ViewCount()->Count);

        $origUA = @$_SERVER["HTTP_USER_AGENT"];
        $_SERVER["HTTP_USER_AGENT"] = 'Googlebot 1.2.3';

        $page1 = $this->objFromFixture('Page', 'page1');
        $response = $this->get($page1->RelativeLink());
        $this->assertFalse($response->isError());
        $page1 = Page::get()->byID($page1->ID);
        $this->assertEquals(1, $page1->ViewCount()->Count, "Bots don't increase count");

        $_SERVER["HTTP_USER_AGENT"] = $origUA;
    }

    public function testOnlyTracksLiveStage()
    {
        $this->markTestIncomplete();
    }
}
