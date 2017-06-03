<?php

namespace TzLion\Muyl;

require_once('vendor/autoload.php');

class MarkupTest extends \PHPUnit_Framework_TestCase
{
    public function testBold()
    {
        $this->assertInputGivesResult("not bold ::bold:: not bold", "<p>not bold <strong>bold</strong> not bold</p>");
    }

    public function testItalic()
    {
        $this->assertInputGivesResult("not italic __italic__ not italic", "<p>not italic <em>italic</em> not italic</p>");
    }

    public function testParagraphBreak()
    {
        $this->assertInputGivesResult("text 1\n\ntext 2", "<p>text 1</p>\n<p>text 2</p>");
    }

    public function testLineBreak()
    {
        $this->assertInputGivesResult("text 1\ntext 2", "<p>text 1<br/>text 2</p>");
    }

    public function testUnorderedList()
    {
        $this->assertInputGivesResult("*meow\n*woof", "<ul>\n<li>meow</li>\n<li>woof</li>\n</ul>");
    }

    public function testOrderedList()
    {
        $this->assertInputGivesResult("#meow\n#woof", "<ol>\n<li>meow</li>\n<li>woof</li>\n</ol>");
    }

    public function testHeaders()
    {
        $this->assertInputGivesResult("=fart=", "<h1>fart</h1>");
        $this->assertInputGivesResult("==fart==", "<h2>fart</h2>");
        $this->assertInputGivesResult("===fart===", "<h3>fart</h3>");
        $this->assertInputGivesResult("====fart====", "<h4>fart</h4>");
        $this->assertInputGivesResult("=====fart=====", "<h5>fart</h5>");
        $this->assertInputGivesResult("======fart======", "<h6>fart</h6>");
    }

    public function testEscapedSpecialChars()
    {
        $this->assertInputGivesResult('\:\_\*\#\[\]\|\=\/\x', "<p>&#58;&#95;&#42;&#35;&#91;&#93;&#124;&#61;&#47;&#120;</p>");
    }

    public function testExternalLinks()
    {
        $this->assertInputGivesResult('[http://lion.li]', "<p><a href='http://lion.li'>http://lion.li</a></p>");
        $this->assertInputGivesResult('[http://lion.li lions world]', "<p><a href='http://lion.li'>lions world</a></p>");
    }

    public function testInternalLinksWithCallback()
    {
        $callback = function ($linkedthing) { return ["url/$linkedthing", "linktext $linkedthing"]; };
        $this->assertInputGivesResult('[[linko|link text]]', "<p><a href='url/linko'>link text</a></p>", false, $callback);
        $this->assertInputGivesResult('[[linko]]', "<p><a href='url/linko'>linktext linko</a></p>", false, $callback);
    }

    public function testImages()
    {
        $this->assertInputGivesResult('{img.jpg Alt text}', "<p><img src='img.jpg' alt='Alt text'/></p>");
        $this->assertInputGivesResult('{img.jpg}', "<p><img src='img.jpg'/></p>");
        $this->assertInputGivesResult('{img.jpg 420x69 Alt text}', "<p><img src='img.jpg' alt='Alt text' style='width:420px;height:69px;'/></p>");
        $this->assertInputGivesResult('{img.jpg 420x69}', "<p><img src='img.jpg' style='width:420px;height:69px;'/></p>");
        $this->assertInputGivesResult('{img.jpg 420x}', "<p><img src='img.jpg' style='width:420px;'/></p>");
        $this->assertInputGivesResult('{img.jpg x69}', "<p><img src='img.jpg' style='height:69px;'/></p>");
        // test the empty width/height cleanup doesnt purge that from random text
        $this->assertInputGivesResult('blah blah height:px; lol', "<p>blah blah height:px; lol</p>");
    }

    public function testHtmlSpecialCharsEscapedIfHtmlDisallowed()
    {
        $this->assertInputGivesResult('<a href="meow">meow</a>', "<p>&lt;a href=&quot;meow&quot;&gt;meow&lt;/a&gt;</p>");
    }

    public function testHtmlSpecialCharsNotEscapedIfHtmlAllowed()
    {
        $this->assertInputGivesResult('<a href="meow">meow</a>', '<a href="meow">meow</a>', true);
        $this->assertInputGivesResult('cats <a href="meow">meow</a> cats', '<p>cats <a href="meow">meow</a> cats</p>', true);
    }

    private function assertInputGivesResult($text, $expectedResult, $htmlOn = false, $internalLinkCallback = null)
    {
        $result = Markup::toHtml($text, $htmlOn, true, true, $internalLinkCallback);
        $this->assertEquals($expectedResult, $result);
    }
}
