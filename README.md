# MUYL Markup Parser

A parser for MUYL, a probably unnecessary markup language for text formatting which I invented to write my own blog posts. Converts MUYL to HTML.

The MUYL language itself and this parser are versioned separately, but since this parser is currently the only implementation and documentation of the language, at least the major and minor versions will probably match up.

Currently the language is at version **0.1.1**.

## Usage

Install it and include it in your project somehow. Use composer, or don't, I'm not the boss of you. It has no dependencies aside from PHPUnit for testing and all the code is in MarkupParser.php.

Then you can either...
```
$markedUpText = '::this text is marked up::';
$parser = new TzLion\Muyl\MarkupParser();
$html = $parser->toHtml($yourMarkedUpText);
```
Or
```
$yourMarkedUpText = '::this text is marked up::';
$html = TzLion\Muyl\MarkupParser::toHtmlStatic($markedUpText);
```

`$markupSpecialChars` is also exposed as a static variable on `MarkupParser`, this is an array containing all special characters used by MUYL if you need them for something.

### Options

Both the MarkupParser constructor and the static method toHtmlStatic take four additional optional parameters, in order:
* `$allowHtml`, default `false`. \
  If `true`, arbitrary HTML from the input text will be preserved. If `false`, HTML special chars will be escaped before parsing.
* `$allowExternalLinks`, default `true`. \
  Enables or disables the external link syntax e.g. `[http://example.com]`. 
* `$allowImages`, default `true`. \
  Enables or disables the image embedding syntax e.g. `{path/to/img.jpg}`
* `$internalLinkCallback`, default `null`. \
  Used in conjunction with the internal link syntax e.g. `[[some-page]]`. A callback taking in some kind of identifier for a page within your app and outputting an array containing 2 elements, a URL and the link text. If not set, internal links will be disabled.
    
## Syntax

Syntax can be found in syntax.txt

## To do 

* Nested lists
* Better config possibly allowing for any feature to be enabled/disabled

## Irony

Yes I am aware of the irony of this readme file for a markup language parser being written in a different markup language to the language parsed by the parser thank you

## Language version history

v0.1.1 - adds the ability to insert line breaks in list items

v0.1.0 - first public release