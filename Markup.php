<?php

namespace TzLion\Muyl;

if ( isset($_POST['text']) ) {
    echo Markup::toHtml($_POST['text'],true);
}

class Markup {

    // from ver 0.0.3 WIP

    public static $markupSpecialChars = array (
        ":", "_", "*", "#", "[", "]", "|", "=", "/", "x"
    );

    public static function toHtml( $text, $allowHtml = false, $allowExternalLinks = true, $allowImages = true ) {

        if (!$allowHtml) {
            $text=htmlspecialchars($text);
        }

        foreach( self::$markupSpecialChars as $char ) {
            $text = str_replace( '\\' . $char, "&#" . ord( $char ) . ";", $text );
        }


        // standardise newline characters
        $text=preg_replace("~(\r\n|\r)~u","\n",$text);

        // lists
        $text = preg_replace("~^\\*(.*)?$~um","<uli>$1</uli>",$text); // uli = fake tag to distinguish unordered list items
        $text = preg_replace("~^#(.*)?$~um","<oli>$1</oli>",$text); // oli = fake tag to distinguish ordered list items
        $text = str_replace("</uli>\n<uli>","</uli><uli>",$text); // strip linebreaks between consecutive tags
        $text = str_replace("</oli>\n<oli>","</oli><oli>",$text);
        $text = preg_replace("~^<(o|u)li>~um","<$1l><$1li>",$text); // opening ol/ul tags
        $text = preg_replace("~</(o|u)li>$~um","</$1li></$1l>",$text); // closing ol/ul tags
        $text = preg_replace("~<(/?)[ou]li>~u","<$1li>",$text); // replace oli/uli w/proper li tags

        // Headers oh snap
        for($h=6;$h>=1;$h--)
            $text = preg_replace("~^={{$h}}(.+?)={{$h}}~um","<h{$h}>$1</h{$h}>",$text);

        // ok NOW lets sort out linebreaks..essentially we wanna apply them to every line that doesnt start & end with tags at this point
        $text = preg_replace("~([^>\n])\n([^<\n])~u","$1<br/>$2",$text); // a BR is a line break surrounded by non-tags and non-newlines
        $text = preg_replace("~^([^<].*[^>])$~um","<p>$1</p>",$text);

        // bold
        $text = preg_replace("~::(.+?)::~su","<strong>$1</strong>",$text);
        // italics
        $text = preg_replace("~__(.+?)__~su","<em>$1</em>",$text);

        // links (internal)
        preg_match_all("~\\[\\[(.*?)/(.*?)(\\|(.*?))?\\]\\]~",$text,$elmatches);
        if ( $elmatches[0] ) {
            for($x=0;$x<count($elmatches[0]);$x++) {
                $fullmatch = $elmatches[0][$x];
                $linkedtype = $elmatches[1][$x];
                $linkeditem = $elmatches[2][$x];
                $linktext = $elmatches[4][$x];
                $url = self::dummyUrlGenerate(null,$linkedtype,$linkeditem);
                if ( !$linktext ) $linktext=$linkedtype."/".$linkeditem; // todo; have this generate the templatised title
                $text = str_replace($fullmatch,"<a href='$url'>$linktext</a>",$text);
            }
        }

        if ( $allowExternalLinks ) {
            // links (external)
            $text = preg_replace("~\\[([^ ]+?)]~u","<a href='$1'>$1</a>",$text); // without text
            $text = preg_replace("~\\[([^ ]+?) (.*?)\\]~u","<a href='$1'>$2</a>",$text); // with
        }

        if ( $allowImages ) {
            // images (external)
            $text = preg_replace("~\\{([^ ]+?)}~u","<img src='$1'/>",$text); // without alt text
            $text = preg_replace("~\\{([^ ]+?) ([0-9]*)x([0-9]*)\\}~u","<img src='$1' style='width:$2px;height:$3px'/>",$text); // with
            $text = preg_replace("~\\{([^ ]+?) ([0-9]*)x([0-9]*) (.*?)\\}~u","<img src='$1' alt='$4' style='width:$2px;height:$3px'/>",$text); // with
            $text = preg_replace("~\\{([^ ]+?) (.*?)\\}~u","<img src='$1' alt='$2'/>",$text); // with
        }

        // Clean up the output linebreak-wise
        // Maybe this should be optional too
        // Maybe the whole thing should be configurable I mean yeah
        // Either a config file or pass i n a config object or this is an obj you instantiate and set config methods on it
        $text = preg_replace("~\n~","",$text);
        $text = preg_replace("~(</p>|</[uo]l>|<[uo]l>|</li>)~","$1\n",$text);

        return trim($text);

    }

    private static function dummyUrlGenerate($thing1,$thing2,$thing3)
    {
        $thing1 = 'internal';
        // This should become a callback later
        // Basically should test against the full internal link syntax because that should be more generic
        // in what the first part can contain
        return "http://$thing1/$thing2/$thing3";
    }

}
