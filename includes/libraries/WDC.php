<?php

// WDC.php Version 1.0

function WrapText($text, $width, $indent) // Recipe 1
{
   // Recipe 1: Wrap Text
   //
   // This recipe takes a string variable containing any
   // text and then adds <br /> and &nbsp; tags in the right
   // places to make the text wrap and indent para starts.
   //
   //    $text:   Text to be modified
   //    $width:  Number of characters art which to wrap
   //    $indent: Number of chars to indent para start

   $wrapped    = "";
   $paragraphs = explode("\n", $text);

   foreach($paragraphs as $paragraph)
   {
      if ($indent > 0) $wrapped .= str_repeat("&nbsp;", $indent);
      
      $words = explode(" ", $paragraph);
      $len   = $indent;

      foreach($words as $word)
      {
         $wlen = strlen($word);

         if (($len + $wlen) < $width)
         {
            $wrapped .= "$word ";
            $len     += $wlen + 1;
         }
         else
         {
            $wrapped  = rtrim($wrapped);
            $wrapped .= "<br />\n$word ";
            $len      =  $wlen;
         }
      }

     $wrapped = rtrim($wrapped);
     $wrapped .= "<br />\n";
   }

   return $wrapped;
}

function CapsControl($text, $type) // Recipe 2
{
   // Recipe 2: Caps Control
   //
   // This recipe takes a string variable containing any
   // text and then changes its case according to the
   // argument $style. The arguments are:
   //
   //    $text: Text to be modified
   //    $style: Must be one of these:
   //       'u' Convert  entirely to upper case (upper)
   //       'l' Convert entirely to lower case (lower)
   //       'w' Capitalize the first letter of each word (word)
   //       's' Capitalize the first letter of each sentence (sentence)
 
   switch($type)
   {
      case "u": return strtoupper($text);
      case "l": return strtolower($text);

      case "w":
         $newtext = "";
         $words   = explode(" ", $text);
         foreach($words as $word)
            $newtext .= ucfirst(strtolower($word)) . " ";
         return rtrim($newtext);

      case "s":
         $newtext   = "";
         $sentences = explode(".", $text);
         foreach($sentences as $sentence)
            $newtext .= ucfirst(ltrim(strtolower($sentence))) . ". ";
         return rtrim($newtext);
   }

   return $text;
}

function FriendlyText($text, $emphasis) // Recipe 3
{
   // Recipe 3: Friendly Text
   //
   // This recipe takes a string variable containing English
   // text, processes it into a 'friendly' form of speech and
   // returns the modified text. It takes these arguments:
   //
   //    $text:     Text to be modified
   //    $emphasis: If TRUE underlines all modifications

   $misc = array("let us", "let's", "i\.e\.", "for example",
      "e\.g\.", "for example", "cannot", "can't", "can not",
      "can't", "shall not", "shan't", "will not", "won't");
   $nots = array("are", "could", "did", "do", "does", "is",
      "had", "has", "have", "might", "must", "should", "was",
      "were", "would");
   $haves = array("could", "might", "must", "should", "would");
   $who = array("he", "here", "how", "I", "it", "she", "that",
      "there", "they", "we", "who", "what", "when", "where",
      "why", "you");
   $what = array("am", "are", "had", "has", "have", "shall",
      "will", "would");
   $contraction = array("m", "re", "d", "s", "ve", "ll", "ll",
      "d");

   for ($j = 0 ; $j < sizeof($misc) ; $j += 2)
   {
      $from = $misc[$j];
      $to   = $misc[$j+1];
      $text = FT_FN1($from, $to, $text, $emphasis);
   }

   for ($j = 0 ; $j < sizeof($nots) ; ++$j)
   {
      $from = $nots[$j] . " not";
      $to   = $nots[$j] . "n't";
      $text = FT_FN1($from, $to, $text, $emphasis);
   }
   
   for ($j = 0 ; $j < sizeof($haves) ; ++$j)
   {
      $from = $haves[$j] . " have";
      $to   = $haves[$j] . "'ve";
      $text = FT_FN1($from, $to, $text, $emphasis);
   }

   for ($j = 0 ; $j < sizeof($who) ; ++$j)
   {
      for ($k = 0 ; $k < sizeof($what) ; ++$k)
      {
         $from = "$who[$j] $what[$k]";
         $to   = "$who[$j]'$contraction[$k]";
         $text = FT_FN1($from, $to, $text, $emphasis);
      }
   }

   $to = "'s";
   $u1 = $u2 = "";

   if ($emphasis)
   {
      $u1 = "<u>";
      $u2 = "</u>";
   }

   return preg_replace("/([\w]*) is([^\w]+)/", "$u1$1$to$u2$2", $text);
}

function FT_FN1($f, $t, $s, $e)
{
   $uf = ucfirst($f);
   $ut = ucfirst($t);
   
   if ($e)
   {
      $t  = "<u>$t</u>";
      $ut = "<u>$ut</u>";
   }
   
   $s   = preg_replace("/([^\w]+)$f([^\w]+)/",  "$1$t$2",  $s);
   return preg_replace("/([^\w]+)$uf([^\w]+)/", "$1$ut$2", $s);
}

function StripWhitespace($text) // Recipe 4
{
   // Recipe 4: Strip Whitespace
   //
   // This recipe takes a string variable containing any
   // text and then strips out all whitespace characters.
   // The arguments are:
   //
   //    $text: Text to be modified
 
   return preg_replace('/\s+/', ' ', $text);
}

function WordSelector($text, $matches, $replace) // Recipe 5
{
   // Recipe 5: Word Selector
   //
   // This recipe takes a string variable containing any
   // text and then matches an selects words within the
   // text, highlighting or removing them.
   // The arguments are:
   //
   //    $text:    Text to be modified
   //    $select:  Array of words to match
   //    $replace: String to replace matches with, or if
   //              string is one of these the text is hilighted:
   //              "u", "b", "i" = underline, bold or italic.

   foreach($matches as $match)
   {
      switch($replace)
      {
         case "u":
         case "b":
         case "i":
            $text = preg_replace("/([^\w]+)($match)([^\w]+)/i",
               "$1<$replace>$2</$replace>$3", $text);
            break;

         default:
            $text = preg_replace("/([^\w]+)$match([^\w]+)/i",
               "$1$replace$2", $text);
            break;
      }
   }

   return $text;
}

function CountTail($number) // Recipe 6
{
   // Recipe 6: Count Tail
   //
   // This recipe takes a number and returns a string
   // based on that number with the correct pluralizer
   // The arguments are:
   //
   //    $number: Number to display
 
   $nstring = (string) $number;
   $pointer = strlen($nstring) - 1;
   $digit   = $nstring[$pointer];
   $suffix  = "th";

   if ($pointer == 0 ||
      ($pointer > 0 && $nstring[$pointer - 1] != 1))
   {
      switch ($nstring[$pointer])
      {
         case 1: $suffix = "st"; break;
         case 2: $suffix = "nd"; break;
         case 3: $suffix = "rd"; break;
      }
   }
   
   return $number . $suffix;
}

function TextTruncate($text, $max, $symbol) // Recipe 7
{
   // Recipe 7: Text Truncate
   //
   // This recipe takes a string variable containing any
   // text and then smartly truncates it to no more than
   // $max characters starting at the previous space.
   // The arguments are:
   //
   //    $text: Text to be modified
   //    $max:  Maximum number of characters

   $temp = substr($text, 0, $max);
   $last = strrpos($temp, " ");
   $temp = substr($temp, 0, $last);
   $temp = preg_replace("/([^\w])$/", "", $temp);
   return "$temp$symbol";
}

function SpellCheck($text, $action) // Recipe 8
{
   // Recipe 8: Spell Check
   //
   // This recipe takes a string variable containing any
   // text and then smartly truncates it to no more than
   // $max characters starting at the previous space.
   // The arguments are:
   //
   //    $text:    Text to be modified
   //    $action:  What to do with unrecognized words:
   //              "u", "b", "i" = Underline, Bold or Italic

   $dictionary = SpellCheckLoadDictionary("dictionary.txt");

   while ($offset < strlen($text))
   {
      preg_match('/[^\w]*([\w]+)[^\w]+/',
         $text, $matches, PREG_OFFSET_CAPTURE, $offset);
      $word   = $matches[1][0];
      $offset = $matches[0][1] + strlen($matches[0][0]);
      
      if (!SpellCheckWord($word, $dictionary))
         $newtext .= "<$action>$word</$action> ";
      else $newtext .= "$word ";
   }
   
   return rtrim($newtext);
}

function SpellCheckLoadDictionary($filename)
{
   return explode("\r\n", file_get_contents($filename));
}

function SpellCheckWord($word, $dictionary)
{
   $top  = sizeof($dictionary) -1;
   $bot  = 0;
   $word = strtolower($word);

   while($top >= $bot)
   {
      $p =   floor(($top + $bot) / 2);
      if     ($dictionary[$p] < $word) $bot = $p + 1;
      elseif ($dictionary[$p] > $word) $top = $p - 1;
      else   return TRUE;
   }
     
   return FALSE;
}

function RemoveAccents($text) // Recipe 9
{
   // Recipe 9: Remove Accents
   //
   // This recipe takes a string variable containing any
   // text and then replaces any accented characters with
   // their non-accented equivalents.
   // The arguments are:
   //
   //    $text: Text to be modified

   $from = array("ç", "æ", "œ", "á", "é", "í", "ó", "ú", "à", "è",
                 "ì", "ò", "ù", "ä", "ë", "ï", "ö", "ü", "ÿ", "â",
                 "ê", "î", "ô", "û", "å", "e", "i", "ø", "u", "Ç",
                 "Æ", "Œ", "Á", "É", "Í", "Ó", "Ú", "À", "È", "Ì",
                 "Ò", "Ù", "Ä", "Ë", "Ï", "Ö", "Ü", "Ÿ", "Â", "Ê",
                 "Î", "Ô", "Û", "Å", "Ø");

   $to =   array("c",  "ae", "oe", "a", "e", "i", "o", "u", "a", "e",
                 "i",  "o",  "u",  "a", "e", "i", "o", "u", "y", "a",
                 "e",  "i",  "o",  "u", "a", "e", "i", "o", "u", "C",
                 "AE", "OE", "A",  "E", "I", "O", "U", "A", "E", "I",
                 "O",  "U",  "A",  "E", "I", "O", "U", "Y", "A", "E",
                 "I",  "O",  "U",  "A", "O");
                 
   return str_replace($from, $to, $text);
}

function ShortenText($text, $size, $mark) // Recipe  10
{
   // Recipe 10: Shorten Text
   //
   // This recipe takes a string variable containing any
   // text and then shortens it to the length supplied by
   // removing text from the middle.
   // The arguments are:
   //
   //    $text: Text to be modified
   //    $size: New size of the string
   //    $mark: String to mark position of removed text

   $len = strlen($text);
   if ($size >= $len) return $text;

   $a = substr($text, 0, $size / 2 -1);
   $b = substr($text, $len - $size / 2 + 1, $size/ 2 -1);
   return $a . $mark . $b;
}

function UploadFile($name, $filetypes, $maxlen) // Recipe 11
{
   // Recipe 11: Upload File
   //
   // This recipe saves an uploaded file to the hard disk
   // The arguments are:
   //
   //    $name:      Name of form field used to upload file
   //    $filetypes: Array of Accepted mime types
   //    $maxlen:    Maximum allowable file size
   //
   // The recipe returns a three-element array, the first of
   // which has one of the following numeric values:
   //
   //     0 = Success
   //    -1 = Upload failed
   //    -2 = Wrong file type
   //    -3 = File too large
   //     1 = File exceeds upload_max_filesize defined in php.ini
   //     2 = File exceeds MAX_FILE_SIZE directive in HTML form
   //     3 = File was only partially uploaded
   //     4 = No file was uploaded
   //     6 = PHP is missing a temporary folder
   //     7 = Failed to write file to disk
   //     8 = File upload stopped by extension
   //
   // Upon success, the second element of the returned
   // array contains the uploaded file type and the third
   // the contents of the file.
   
   if (!isset($_FILES[$name]['name']))
      return array(-1, NULL, NULL);

   if (!in_array($_FILES[$name]['type'], $filetypes))
      return array(-2, NULL, NULL);
 
   if ($_FILES[$name]['size'] > $maxlen)
      return array(-3, NULL, NULL);

   if ($_FILES[$name]['error'] > 0)
      return array($_FILES[$name]['error'], NULL, NULL);
      
   $temp = file_get_contents($_FILES[$name]['tmp_name']);
   return array(0, $_FILES[$name]['type'], $temp);
}

function ImageResize($image, $w, $h) // Recipe 12
{
   // Recipe 12: Image Resize
   //
   // This recipe takes a GD image and resizes it to the
   // required dimensions. The arguments are:
   //
   //    $image: The image source
   //    $w:     New width 
   //    $h:     New height
   
   $oldw = imagesx($image);
   $oldh = imagesy($image);

   $temp = imagecreatetruecolor($w, $h);
   imagecopyresampled($temp, $image, 0, 0, 0, 0, $w, $h, $oldw, $oldh);
   return $temp;
}

function MakeThumbnail($image, $max) // Recipe 13
{
   // Recipe 13: Make Thumbnail
   //
   // This recipe takes a GD image and returns a copy as
   // a thumbnail. The arguments are:
   //
   //    $image: The image source
   //    $max:   Maximum width and height
   
   $thumbw = $w = imagesx($image);
   $thumbh = $h = imagesy($image);

   if ($w > $h && $max < $w)
   {
      $thumbh = $max / $w * $h;
      $thumbw = $max;
   }
   elseif ($h > $w && $max < $h)
   {
      $thumbw = $max / $h * $w;
      $thumbh = $max;
   }
   elseif ($max < $w)
   {
      $thumbw = $thumbh = $max;
   }

   return ImageResize($image, $thumbw, $thumbh);
}

function ImageAlter($image, $effect) // Recipe 14
{
   // Recipe 14: Image Alter
   //
   // This recipe takes a GD image and modifies it
   // according to the selected effect. The arguments are:
   //
   //    $image:  The image source
   //    $effect: The effect to use between 1 and 14:
   //        1 = Sharpen
   //        2 = Blur
   //        3 = Brighten
   //        4 = Darken
   //        5 = Increase Contrast
   //        6 = Decrease Contrast
   //        7 = Grayscale
   //        8 = Invert
   //        9 = Increase Red
   //       10 = Increase Green
   //       11 = Increase Blue
   //       12 = Edge Detect
   //       13 = Emboss
   //       14 = Sketchify

   switch($effect)
   {
      case 1:  imageconvolution($image, array(array(-1, -1, -1),
                  array(-1, 16, -1), array(-1, -1, -1)), 8, 0);
               break;
      case 2:  imagefilter($image,
                  IMG_FILTER_GAUSSIAN_BLUR); break;
      case 3:  imagefilter($image,
                  IMG_FILTER_BRIGHTNESS, 20); break;
      case 4:  imagefilter($image,
                  IMG_FILTER_BRIGHTNESS, -20); break;
      case 5:  imagefilter($image,
                  IMG_FILTER_CONTRAST, -20); break;
      case 6:  imagefilter($image,
                  IMG_FILTER_CONTRAST, 20); break;
      case 7:  imagefilter($image,
                  IMG_FILTER_GRAYSCALE); break;
      case 8:  imagefilter($image,
                  IMG_FILTER_NEGATE); break;
      case 9:  imagefilter($image,
                  IMG_FILTER_COLORIZE, 128, 0, 0, 50); break;
      case 10: imagefilter($image,
                  IMG_FILTER_COLORIZE, 0, 128, 0, 50); break;
      case 11: imagefilter($image,
                  IMG_FILTER_COLORIZE, 0, 0, 128, 50); break;
      case 12: imagefilter($image,
                  IMG_FILTER_EDGEDETECT); break;
      case 13: imagefilter($image,
                  IMG_FILTER_EMBOSS); break;
      case 14: imagefilter($image,
                  IMG_FILTER_MEAN_REMOVAL); break;
   }
   
   return $image;
}

/*function ImageCrop($image, $x, $y, $w, $h) // Recipe 15
{
   // Recipe 15: Image Crop
   //
   // This recipe takes a GD image and returns a cropped
   // version of it. The arguments are:
   //
   //    $image:   The image source
   //    $x & $y:  The top-left corner
   //    $w & $h : The width and height

   $temp = imagecreatetruecolor($w, $h);
   imagecopyresampled($temp, $image, 0, 0, $x, $y, $w, $h, $w, $h);
   return $temp;
}*/

function ImageEnlarge($image, $w, $h, $smoothing) // Recipe 16
{
   // Recipe 16: Image Enlarge
   //
   // This recipe takes a GD image and enlarges it to the
   // required dimensions through several resamples.
   // The arguments are:
   //
   //    $image: The image source
   //    $w:     New width 
   //    $h:     New height
   //    $smoothing: The amount to smooth by (0-90):
   //        0 = Minimum smoothing
   //       90 = Maximum smoothing
   
   $oldw  = imagesx($image);
   $oldh  = imagesy($image);
   $step  = 3.1415927 * ((100 - $smoothing) / 100);
   $max   = $w / $step;
   $ratio = $h / $w;
   
   for ($j = $oldw ; $j < $max; $j += $step)
      $image = ImageResize($image, $j * $step,
         $j * $step * $ratio);

   return ImageResize($image, $w, $h);
}

function ImageDisplay($filename, $type, $quality) // Recipe 17
{
   // Recipe 17: Image Display
   //
   // This recipe takes an image file name, loads it
   // and displays it to a browser. The image can be
   // converted on the fly to different types and quality.
   // The arguments required are:
   //
   //    $filename: Path and/or name of image to display
   //
   //    $type: Type of file to display:
   //
   //       "":     Output as current type
   //       "gif":  Output as a gif image
   //       "jpeg": Output as a jpeg image
   //       "png":  Output as a png image
   //
   //    $quality: Quality of image (0-99).
   //              Only used if $type is jpeg or png
   //
   //        0 = Lowest quality and smallest size
   //       99 = Best quality and largest size

   $contents = file_get_contents($filename);
   
   if ($type == "")
   {
      $filetype = getimagesize($filename);
      $mime     = image_type_to_mime_type($filetype[2]);
      header("Content-type: $mime");
      die($contents);
   }

   $image = imagecreatefromstring($contents);
   header("Content-type: image/$type");
   
   switch($type)
   {
      case "gif" : imagegif($image);                  break;
      case "jpeg": imagejpeg($image, NULL, $quality); break;
      case "png" : imagepng($image,  NULL,
                     round(9 - $quality * .09));      break;
   }
}

function ImageConvert($fromfile, $tofile, $type, $quality) // Recipe 18
{
   // Recipe 18: Image Convert
   //
   // This recipe takes an image file name, then loads,
   // converts and saves it as a new file. Arguments are:
   //
   //    $fromfile: Path and/or name of image to convert from
   //    $tofile:   Path and/or name of image to convert to
   //    $type:     Type of file to convert to:
   //
   //          "gif":  Convert to a gif image
   //          "jpeg": Convert to a jpeg image
   //          "png":  Convert to a png image
   //
   //    $quality:  Quality of image (0-99).
   //               Only used if $type is jpeg or png
   //
   //          0  =    Lowest quality and smallest size
   //          99 =    Best quality and largest size

   $contents = file_get_contents($fromfile);
   $image    = imagecreatefromstring($contents);

   switch($type)
   {
      case "gif":  imagegif($image,  $tofile); break;
      case "jpeg": imagejpeg($image, $tofile, $quality); break;
      case "png":  imagepng($image,  $tofile,
                     round(9 - $quality * .09)); break;
   }
}

function GifText($file, $text, $font, $size, $fore, $back, $shadow, $shadowcolor) // Recipe 19
{
   // Recipe 19: Gif Text
   //
   // This recipe accepts text input and then turns it into
   // a gif image. Various font sizes and effects are available
   // The arguments are:
   //
   //    $file:        The path and file to save the finished gif
   //    $text:        The text to display
   //    $font:        Filename of a TTF font file
   //    $size:        Font size to use
   //    $fore:        The foreground color
   //    $back:        The background color
   //    $shadow:      0 = None, 1 or more = The offset to use
   //    $shadowcolor: The shadow color (if selected)

   $bound  = imagettfbbox($size, 0, $font, $text);
   $width  = $bound[2] + $bound[0] + 6 + $shadow;
   $height = abs($bound[1]) + abs($bound[7]) + 5 + $shadow;
   $image  = imagecreatetruecolor($width, $height);
   $bgcol  = GD_FN1($image, $back);
   $fgcol  = GD_FN1($image, $fore);
   $shcol  = GD_FN1($image, $shadowcolor);
   imagefilledrectangle($image, 0, 0, $width, $height, $bgcol);
   
   if ($shadow > 0) imagettftext($image, $size, 0, $shadow + 2,
      abs($bound[5]) + $shadow + 2, $shcol, $font, $text);
   
   imagettftext($image, $size, 0, 2, abs($bound[5]) + 2, $fgcol,
      $font, $text);
   imagegif($image, $file);
}

function ImageWatermark($fromfile, $tofile, $type, $quality, $text, $font, $size, $fore, $opacity) // Recipe 20
{
   // Recipe 20: Image Watermark
   //
   // This recipe takes a supplied image and then adds a
   // watermark to it. Several options are available.
   // The arguments are:
   //
   //    $fromfile: The filename of the original image
   //    $tofile:   The filename for the watermarked image
   //    $type:     One of jpeg, gif or png
   //    $quality:  Quality of the saved image (0-99):
   //               Only used if $type is jpeg or png
   //        0 = Lowest quality and smallest size
   //       99 = Best quality and largest size
   //    $text:     The text to display
   //    $font:     Filename of a TTF font file
   //    $size:     Font size to use
   //    $fore:     The foreground color
   //    $opacity:  Opacity of watermark (0-100)
   //        0 = No opacity, maximum transparency (invisible)
   //      100 = Maximum opacity, no transparency (fully visible)

   $contents = file_get_contents($fromfile);
   $image1   = imagecreatefromstring($contents);
   $bound    = imagettfbbox($size, 0, $font, $text);
   $width    = $bound[2] + $bound[0] + 6;
   $height   = abs($bound[1]) + abs($bound[7]) + 5;
   $image2   = imagecreatetruecolor($width, $height);
   $bgcol    = GD_FN1($image2, "fedcba");
   $fgcol    = GD_FN1($image2, $fore);
 
   imagecolortransparent($image2, $bgcol);
   imagefilledrectangle($image2, 0, 0, $width, $height, $bgcol);
   imagettftext($image2, $size, 0, 2, abs($bound[5]) + 2,
      $fgcol, $font, $text);
   imagecopymerge($image1, $image2,
      (imagesx($image1) - $width) / 2,
      (imagesy($image1) - $height) / 2,
      0, 0, $width, $height, $opacity);

   switch($type)
   {
      case "gif":  imagegif($image1,  $tofile); break;
      case "jpeg": imagejpeg($image1, $tofile, $quality); break;
      case "png":  imagepng($image1,  $tofile,
                     round(9 - $quality * .09)); break;
   }
}

function GD_FN1($image, $color)
{
   return imagecolorallocate($image,
      hexdec(substr($color, 0, 2)),
      hexdec(substr($color, 2, 2)),
      hexdec(substr($color, 4, 2)));
}

function RelToAbsURL($page, $url) // Recipe 21
{
   // Recipe 21: Relative To Absolute URL
   //
   // This recipe accepts the absolute URL of a web page
   // and a link featured within that page. The link is then
   // turned into an absolute URL which can be independently
   // accessed. Only applies to http:// URLs. Arguments are:
   //
   //    $page: The web page containing the URL
   //    $url:  The URL to convert to absolute

   if (substr($page, 0, 7) != "http://") return $url;
   
   $parse = parse_url($page);
   $root  = $parse['scheme'] . "://" . $parse['host'];
   $p     = strrpos(substr($page, 7), '/');
   
   if ($p) $base = substr($page, 0, $p + 8);
   else    $base = "$page/";
   
   if (substr($url, 0, 1) == '/')           $url = $root . $url;
   elseif (substr($url, 0, 7) != "http://") $url = $base . $url;
   
   return $url;
}

function GetLinksFromURL($page) // Recipe 22
{
   // Recipe 22: Get Links From URL
   //
   // This recipe accepts the URL or a web page and returns
   // an array of all the links found in it. The argument is:
   //
   //    $page: The web site's main URL

   $contents = @file_get_contents($page);
   if (!$contents) return NULL;
   
   $urls    = array();
   $dom     = new domdocument();
   @$dom    ->loadhtml($contents);
   $xpath   = new domxpath($dom);
   $hrefs   = $xpath->evaluate("/html/body//a");

   for ($j = 0 ; $j < $hrefs->length ; $j++)
      $urls[$j] = RelToAbsURL($page,
         $hrefs->item($j)->getAttribute('href'));

   return $urls;
}

function CheckLinks($page, $timeout, $runtime) // Recipe 23
{
   // Recipe 23: Check Links
   //
   // This recipe accepts a path/filename or a URL to an HTML
   // web page containing links to be tested. It returns an
   // array, the first element of which is an integer which is
   // set to 0 if all links worked, otherwise it is set to the
   // number of bad links. The second element is an array
   // containing the bad link URLs. The arguments are:
   //
   //    $page:    The web page to check. This must end in
   //              either a filename.ext or a /
   //    $timeout: Seconds to wait for a page to be returned
   //    $runtime: Maximum number of seconds script can run
   //
   // Note that some sites may not allow pages to be grabbed
   // in this manner and will result in a URL failing because
   // of this, this includes pages requiring authentication.

   ini_set('max_execution_time', $runtime);
   $contents = @file_get_contents($page);
   if (!$contents) return array(1, array($page));
   
   $checked = array();
   $failed  = array();
   $fail    = 0;
   $urls    = GetLinksFromURL($page);
   $context = stream_context_create(array('http' =>
      array('timeout' => $timeout))); 
      
   for ($j = 0 ; $j < count($urls); $j++)
   {
      if (!in_array($urls[$j], $checked))
      {
         $checked[] = $urls[$j];

         // Uncomment the following line to view progress
         // echo " $urls[$j]<br />\n"; ob_flush(); flush();

         if (!@file_get_contents($urls[$j], 0, $context, 0, 256))
            $failed[$fail++] = $urls[$j];
      }
   }

   return array($fail, $failed);
}

function DirectoryList($path) // Recipe 24
{
   // Recipe 24: Directory List
   //
   // This recipe accepts a path to a directory on the hard
   // disk and returns the list of files located there.
   //
   //    $path: The directory to list
   //
   // The function returns an array with the first element set
   // to the number of subdirectories in the directory and the
   // second to the number of files. If none are found then
   // 0 is returned. The third and fourth elements returned
   // contain arrays with all the directories and file names
   // respectively.

   $files = array();
   $dirs  = array();
   $fnum  = $dnum = 0;

   if (is_dir($path))
   {
      $dh = opendir($path);

      do
      {
         $item = readdir($dh);

         if ($item !== FALSE && $item != "." && $item != "..")
         {
            if (is_dir($item)) $dirs[$dnum++]  = $item;
            else               $files[$fnum++] = $item;
         }
      } while($item !== FALSE);
   
      closedir($dh);
   }

   return array($dnum, $fnum, $dirs, $files);
}

function QueryHighlight($text, $highlight) // Recipe 25
{
   // Recipe 25: Query Highlight
   //
   // This recipe text to be highlighted if this page was
   // reached as a result of a search engine query. It takes
   // the arguments:
   //
   //    $text:      The text to display
   //    $highlight: The highlight to use:
   //                "b", "i" or "u": Bold, italic or
   //                underlined

   $refer = getenv('HTTP_REFERER');
   $parse = parse_url($refer);

   if ($refer == "") return $text;
   elseif (!isset($parse['query'])) return $text;

   $queries = explode('&', $parse['query']);

   foreach($queries as $query)
   {
      list($key, $value) = explode('=', $query);

      if ($key == "q" || $key == "p")
      {
         $matches = explode(' ', preg_replace('/[^\w ]/', '',
            urldecode($value)));
         return WordSelector($text, $matches, $highlight);
      }
   }
}

function RollingCopyright($message, $year) // Recipe 26
{
   // Recipe 26: Rolling Copyright
   //
   // This recipe takes a string variable containing the year
   // a copyright commenced and returns a copyright message
   // that's always up to date. The arguments are:
   //
   //    $message: Text to be modified
   //    $year:    Start year of copyright

   date_default_timezone_set('UTC');
   return "$message &copy;$year-" . date("Y");
}

function EmbedYouTubeVideo($id, $width, $height, $high, $full, $auto) // Recipe 27
{
   // Recipe 27: Embed YouTube Video
   //
   // Displays a YouTube video. The arguments required are:
   //
   //    $id:     The YouTube video id
   //    $width:  The display width
   //    $height: The display height
   //    $high:   'true' or 1 for high quality
   //    $full:   'true' or 1 if full screen zoom allowed
   //    $auto:   'true' or 1 for auto start
   
   if ($width && !$height) $height = $width  * 0.7500;
   if (!$width && $height) $width  = $height * 1.3333;
   if (!$width)            $width  = 480;
   if (!$height)           $height = 385;

   $fs = ($full) ? 'allowfullscreen'    : '';
   $hd = ($high) ? '?hd=1'              : '';
   $as = ($hd)   ? '?'                  : '&';
   $ap = ($auto) ? "$as" . 'autoplay=1' : '';

   return "<iframe class='youtube-player' type='text/html' " .
   "width='$width' height='$height' " .
   "src='http://www.youtube.com/embed/$id$hd$ap' $fs></iframe>";
}

function CreateList($items, $start, $type, $bullet) // Recipe 28
{
   // Recipe 28: Create List
   //
   // This recipe accepts an array containing a list of
   // items to be displayed in an HTML list, along with some
   // arguments specifying the format, and returns the HTML
   // for the completed list. Arguments are:
   //
   //    $items:  An array containing a list of items
   //    $start:  The start number if displaying ordered list
   //    $type:   The type of list. Either "ol" for ordered,
   //             or "ul" for unordered.
   //    $bullet: The type of bullet. If $type is "ol" the
   //             bullet types can be "1" for numeric, "A"
   //             for upper case alphabetic, "a" for lower
   //             case alphabetic, "I" for upper case Roman,
   //             or "i for lower case Roman numerals. If
   //             $type is "ul" then $bullet can be one of
   //             "disc", "square" or "circle".

   $list = "<$type start='$start' type='$bullet'>";
   foreach ($items as $item) $list .= "<li>$item</li>";
   return $list . "</$type>";
}

function HitCounter($filename, $action) // Recipe 29
{
   // Recipe 29: Hit Counter
   //
   // This recipe accepts the filename of a counter which is
   // incremented or read back. optionally a folder and init-
   // ialization number can be passed. The arguments are:
   //
   //    $filename: Path/file name to save the counter details
   //               Must be unique to each counter.
   //    $action:   "reset"  = reset counts,
   //               "add"    = increment counts
   //               "get"    = return counts
   //               "delete" = delete counter
   //
   // This recipe returns an array. If $action is "get" the
   // 1st and 2nd elements contain the raw and unique hit
   // counts. Otherwise the return value is indeterminate.

   $data = getenv("REMOTE_ADDR") .
           getenv("HTTP_USER_AGENT") . "\n";
   
   switch ($action)
   {
      case "reset":
         $fp = fopen($filename, "w");
         if (flock($fp, LOCK_EX))
            ;
         flock($fp, LOCK_UN);
         fclose($fp);
         return;

      case "add":
         $fp = fopen($filename, "a+");
         if (flock($fp, LOCK_EX))
            fwrite($fp, $data);
         flock($fp, LOCK_UN);
         fclose($fp);
         return;

      case "get":
         $fp = fopen($filename, "r");
         if (flock($fp, LOCK_EX))
            $file = fread($fp, filesize($filename) - 1);
         flock($fp, LOCK_UN);
         fclose($fp);
         $lines  = explode("\n", $file);
         $raw    = count($lines);
         $unique = count(array_unique($lines));
         return array($raw, $unique);

      case "delete":
         unlink($filename);
         return;
   }
}

function RefererLog($filename, $action) // Recipe 30
{
   // Recipe 30: Referer Log
   //
   // This recipe accepts the filename of a logfile in which
   // to store URLs that refer to the current page. The
   // arguments are:
   //
   //    $filename: Path/file name to save the logfile.
   //    $action:   "reset"  = reset logfile,
   //               "add"    = add current referer
   //               "get"    = return unique logfile entries
   //               "delete" = delete logfile
   //
   // If $action is "get" this recipe returns an array with
   // all the unique referers. Otherwise the return value is
   // indeterminate.
   
   $data = getenv("HTTP_REFERER") . "\n";
   if ($data == "\n") $data = " No Referrer\n";

   switch ($action)
   {
      case "reset":
         $fp = fopen($filename, "w");
         if (flock($fp, LOCK_EX))
            ;
         flock($fp, LOCK_UN);
         fclose($fp);
         return;

      case "add":
         $fp = fopen($filename, "a+");
         if (flock($fp, LOCK_EX))
            fwrite($fp, $data);
         flock($fp, LOCK_UN);
         fclose($fp);
         return;

      case "get":
         $fp = fopen($filename, "r");
         if (flock($fp, LOCK_EX))
            $file = fread($fp, filesize($filename) -1);
         flock($fp, LOCK_UN);
         fclose($fp);
         $temp = array_unique(explode("\n", $file));
         sort($temp);
         return $temp;

      case "delete":
         unlink($filename);
         return;
   }
}

function EvaluateExpression($expr) // Recipe 31
{
   // Recipe 31: Evaluate Expression
   //
   // This recipe accepts a string containing an arithmetic
   // expression and returns the result of evaluating it.
   // Over 20 functions are also supported. The argument
   // required is:
   //
   //    $expr: The arithmetic expression
   
   $f1 = array ('abs',   'acos',  'acosh', 'asin',  'asinh',
                'atan',  'atan2', 'atanh', 'cos',   'cosh',
                'exp',   'expm1', 'log',   'log10', 'log1p',
                'pi',    'pow',   'sin',   'sinh',  'sqrt',
                'tan',   'tanh');

   $f2 = array ('!01!',  '!02!',  '!03!',  '!04!',  '!05!',
                '!06!',  '!07!',  '!08!',  '!09!',  '!10!',
                '!11!',  '!12!',  '!13!',  '!14!',  '!15!',
                '!16!',  '!17!',  '!18!',  '!19!',  '!20!',
                '!21!',  '!22!');

   $expr = strtolower($expr);
   $expr = str_replace($f1, $f2, $expr);
   $expr = preg_replace("/[^\d\+\*\/\-\.(),! ]/", '', $expr);
   $expr = str_replace($f2, $f1, $expr);

   // Uncomment the line below to see the sanitized expression
   // echo "$expr<br />\n";

   return eval("return $expr;");
}

function ValidateCC($number, $expiry) // Recipe 32
{
   // Recipe 32: Validate Credit Card
   //
   // This recipe accepts a credit card number and
   // an expiry date and returns TRUE or FALSE,
   // depending on whether the details pass date
   // and checksum validation. The arguments are:
   //
   //    $number: Credit Card Number
   //    $expiry: Expiry date in the form:
   //       07/12 or 0712 (for July, 2012)

   $number = preg_replace('/[^\d]/', '', $number);
   $expiry = preg_replace('/[^\d]/', '', $expiry);
   $left   = substr($number, 0, 4);
   $cclen  = strlen($number);
   $chksum = 0;

   // Diners Club
   if (($left >= 3000) && ($left <= 3059) ||
       ($left >= 3600) && ($left <= 3699) ||
       ($left >= 3800) && ($left <= 3889))
      if ($cclen != 14) return FALSE;

   // JCB
   if (($left >= 3088) && ($left <= 3094) ||
       ($left >= 3096) && ($left <= 3102) ||
       ($left >= 3112) && ($left <= 3120) ||
       ($left >= 3158) && ($left <= 3159) ||
       ($left >= 3337) && ($left <= 3349) ||
       ($left >= 3528) && ($left <= 3589))
      if ($cclen != 16) return FALSE;

   // American Express
   elseif (($left >= 3400) && ($left <= 3499) ||
           ($left >= 3700) && ($left <= 3799))
      if ($cclen != 15) return FALSE;

   // Carte Blanche
   elseif (($left >= 3890) && ($left <= 3899))
      if ($cclen != 14) return FALSE;

   // Visa
   elseif (($left >= 4000) && ($left <= 4999))
      if ($cclen != 13 && $cclen != 16) return FALSE;

   // MasterCard
   elseif (($left >= 5100) && ($left <= 5599))
      if ($cclen != 16) return FALSE;
      
   // Australian BankCard
   elseif ($left == 5610)
      if ($cclen != 16) return FALSE;

   // Discover
   elseif ($left == 6011)
      if ($cclen != 16) return FALSE;

   // Unknown
   else return FALSE;

   for ($j = 1 - ($cclen % 2); $j < $cclen; $j += 2)
      $chksum += substr($number, $j, 1);

   for ($j = $cclen % 2; $j < $cclen; $j += 2)
   {
      $d = substr($number, $j, 1) * 2;
      $chksum += $d < 10 ? $d : $d - 9;
   }

   if ($chksum % 10 != 0) return FALSE;

   if (mktime(0, 0, 0, substr($expiry, 0, 2), date("t"),
      substr($expiry, 2, 2)) < time()) return FALSE;
   
   return TRUE;
}

function CreateCaptcha($size, $length, $font, $readfolder, $writefolder, $salt1, $salt2) // Recipe 33
{
   // Recipe 33: Create Captcha
   //
   // This recipe creates a GIF image containing a word the
   // user must type in to prove they are not a program. The
   // arguments are:
   //
   //    $size:   Font size for the Captcha
   //    $length: Length of Captcha in letters
   //    $font:   Location of a TrueType font
   //    $folder: Location of a temporary, web-
   //             accessible folder to store the
   //             captcha GIF. Must end with /
   //    $salt1:  A sequence of characters to help
   //             make the Captcha uncrackable
   //    $salt2:  A second sequence to make it even
   //             less crackable
   //
   // The function returns a three element array containing the
   // following:
   //    Element 0: The Captcha word to be entered
   //    Element 1: A unique 32 character token
   //    Element 2: The location of a GIF file with the Captcha
   //               text
   //
   // The function expects a file dictionary.txt to exist in the
   // current folder. This must be a text file of words separated
   // by \r\n carriage return, line feed pairs.

   $file    = file_get_contents($readfolder . 'dictionary.txt');
   $temps   = explode("\r\n", $file);
   $dict    = array();

   foreach ($temps as $temp)
      if (strlen($temp) == $length)
         $dict[] = $temp;

   $captcha = $dict[rand(0, count($dict) - 1)];
   $token   = sha1("$salt1$captcha$salt2");
   $fname   = "$writefolder$token.gif";
   GifText($fname, $captcha, $readfolder.$font, $size, "444444",
      "ffffff", $size / 10, "666666");
   $image   = imagecreatefromgif($fname);
   $image   = ImageAlter($image, 2);
   $image   = ImageAlter($image, 13);
   
   for ($j = 0 ; $j < 3 ; ++$j)
      $image = ImageAlter($image, 3);
   for ($j = 0 ; $j < 2 ; ++$j)
      $image = ImageAlter($image, 5);

   imagegif($image, $fname);
   return array($captcha, $token, $fname);
}

function CheckCaptcha($captcha, $token, $salt1, $salt2) // Recipe 34
{
   // Recipe 34: Check Captcha
   //
   // This recipe takes a Captcha string as entered by a user,
   // along with a special token and filename
   // to verify the user as human. The arguments are:
   //
   //    $captcha: Captcha as typed by user
   //    $token:   Token supplied by CreateCaptcha
   //    $image:   Image location supplied by CreateCaptcha
   //    $salt1:   Same as supplied to CreateCaptcha
   //    $salt2:   Same as supplied to CreateCaptcha
   //
   // The recipe returns TRUE if the Captcha matches, otherwise
   // FALSE. It also deletes the temporary GIF file if it exists.
   
   return $token == sha1("$salt1$captcha$salt2");
}

function ValidateText($text, $minlength, $maxlength, $allowed, $required) // Recipe 35
{
   // Recipe 35: Validate Text
   //
   // This recipe takes a string and parameters defining its
   // minimum and maximum length, and the allowed characters.
   // The arguments are:
   //
   //    $text:      The text to be validate
   //    $minlength: The minimum allowed length
   //    $maxlength: The maximum allowed length
   //    $allowed:   The allowed characters. Can include regexp
   //                strings such as a-zA-Z0-9 or \w. Characters
   //                used in regular expressions but which are
   //                to be allowed (such as ( and [ etc) should
   //                be escaped, like this: \( and \[.
   //    $required:  The required characters. This argument
   //                is a string containing one or more of the
   //                letters a, l, u, d, w or p for any letter,
   //                lower case, upper case, digit, word (any
   //                lower, upper, digit or _) or punctuation.
   //                For each of these included, at least one of
   //                that type of character must be in the string
   //
   // The recipe returns an array of two elements if the string
   // does not validate. The first has the value FALSE and the
   // second is an array of error messages. If it does validate
   // Only one element is returned and its value is TRUE.
   
   $len   = strlen($text);
   $error = array();
   
   if ($len < $minlength)
      $error[] = "The string length is too short " . 
         "(min $minlength characters)";
   elseif ($len > $maxlength)
      $error[] = "The string length is too long " .
         "(max $maxlength characters)";
   
   $result = preg_match_all("/([^$allowed])/", $text, $matches);
   $caught = implode(array_unique($matches[1]), ', ');
   $plural = strlen($caught) > 1 ? $plural = "s are" : " is";

   if ($result) $error[] = "The following character$plural " .
      "not allowed: " . $caught;

   for ($j = 0 ; $j < strlen($required) ; ++$j)
   {
      switch(substr(strtolower($required), $j, 1))
      {
         case "a": $regex = "a-zA-Z"; $str = "letter";
                   break;
         case "l": $regex = "a-z";    $str = "lower case";
                   break;
         case "u": $regex = "A-Z";    $str = "upper case";
                   break;
         case "d": $regex = "0-9";    $str = "digit";
                   break;
         case "w": $regex = "\w";     $str = "letter, number or _";
                   break;
         case "p": $regex = "\W";     $str = "punctuation";
                   break;
      }

      if (!preg_match("/[$regex]/", $text))
         $error[] = "The string must include at least one " .
            "$str character";
   }

   if (count($error)) return array(FALSE, $error);
   else               return array(TRUE);
}

function ValidateEmail($email) // Recipe 36
{
   // Recipe 36: Validate Email
   //
   // This recipe takes an email address and determines whether
   // it appears to be valid. The argument is:
   //
   //    $email: An email address to validate
   
   $at = strrpos($email, '@');
   
   if (!$at || strlen($email) < 6) return FALSE;
   
   $left  = substr($email, 0, $at);
   $right = substr($email, $at + 1);
   $res1  = ValidateText($left,  1, 64,  "\w\.\+\-",       "a");
   $res2  = ValidateText($right, 1, 255, "\a-zA-Z0-9\.\-", "a");
  
   if (!strpos($right, '.') || !$res1[0] || !$res2[0])
      return FALSE;
   else return TRUE;
}

function SpamCatch($text, $words) // Recipe 37
{
   // Recipe 37: Spam Catch
   //
   // This recipe takes a string of text and compares it
   // against a set of known spam keywords. It returns 0
   // if no keywords are encountered or an integer indicating
   // how likely the text is spam: 0-10 = slightly worrying,
   // 10-20 = possibly spam, 20-30 = likely spam, 30-40 =
   // probably spam, 40+ almost definitely spam.
   // The arguments required are:
   //
   //    $text:  The text to spam check
   //    $words: An array of commonly used spam keywords

   return strlen($text) - strlen(WordSelector($text, $words, ''));
}

function SendEmail($message, $subject, $from, $replyto, $to, $cc, $bcc, $type) // Recipe 38
{
   // Recipe 38: Send Email
   //
   // This recipe sends an email to one or more recipients.
   // It returns TRUE on success, otherwise FALSE. The
   // arguments required are:
   //
   //    $message: The message to send - required
   //    $subject: The message's subject - required
   //    $from:    The sender's email address - required
   //    $replyto: The address for replies. This can be set
   //              to NULL or "" if replies should be returned
   //              to the sender.
   //    $to:      The recipient's email address - required
   //    $cc:      An array of email addresses to send copies
   //              to. Can be set to NULL.
   //    $bcc:     An array of email addresses to send hidden
   //              copies to. Can be set to NULL.
   //    $type:    If set to HTML then the message is sent as
   //              HTML.

   $headers = "From: $from\r\n";

   if (strtolower($type) == "html")
   {
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
   }
   
   if ($priority > 0)  $headers .= "X-Priority: $priority\r\n";
   if ($replyto != "") $headers .= "Reply-To: $replyto\r\n";

   if (count($cc))
   {
      $headers .= "Cc: ";
         for ($j = 0 ; $j < count($cc) ; ++$j)
            $headers .= $cc[$j] . ",";
      $headers = substr($headers, 0, -1) . "\r\n";
   }

   if (count($bcc))
   {
      $headers .= "Bcc: ";
         for ($j = 0 ; $j < count($bcc) ; ++$j)
            $headers .= $bcc[$j] . ",";
      $headers = substr($headers, 0, -1) . "\r\n";
   }

   return mail($to, $subject, $message, $headers);
}

function BBCode($string) // Recipe 39
{
   // Recipe 39: BB Code
   //
   // This recipe recognizes and translates BB Code
   // into its HTML equivalent. Arguments required are:
   //
   //    $string: A string containing BB Code

   $from   = array('[b]', '[/b]',  '[i]', '[/i]',
                   '[u]', '[/u]',  '[s]', '[/s]',
                   '[quote]',      '[/quote]',
                   '[code]',       '[/code]',
                   '[img]',        '[/img]',
                   '[/size]',      '[/color]',
                   '[/url]');
   $to     = array('<b>', '</b>',  '<i>', '</i>',
                   '<u>', '</u>',  '<s>', '</s>',
                   '<blockquote>', '</blockquote>',
                   '<pre>',        '</pre>',
                   '<img src="',   '" />',
                   '</span>',      '</font>',
                   '</a>');
   $string = str_replace($from, $to, $string);
   $string = preg_replace("/\[size=([\d]+)\]/",
      "<span style=\"font-size:$1px\">", $string);
   $string = preg_replace("/\[color=([^\]]+)\]/",
      "<font color='$1'>", $string);
   $string = preg_replace("/\[url\]([^\[]*)<\/a>/",
      "<a href='$1'>$1</a>", $string);
   $string = preg_replace("/\[url=([^\]]*)]/",
      "<a href='$1'>", $string);
   return $string;
}

function PoundCode($text) // Recipe 40
{
   // Recipe 40: Pound Code
   //
   // This recipe recognizes and translates Pound Code
   // (also known as hash code) into its HTML equivalent.
   // Arguments required are:
   //
   //    $text:    A string containing Pound Code

   $names = array('#georgia', '#arial',   '#courier',
                  '#script',  '#impact',  '#comic',
                  '#chicago', '#verdana', '#times');
   $fonts = array('Georgia',  'Arial',    'Courier New',
                  'Script',   'Impact',   'Comic Sans MS',
                  'Chicago',  'Verdana',  'Times New Roman');
   $to    = array();
   
   for ($j = 0 ; $j < count($names) ; ++$j)
      $to[] = "<font face='$fonts[$j]'>";
      
   $text = str_ireplace($names,          $to,                 $text);
   $text = preg_replace('/#([bius])-/i', "</$1>",             $text);
   $text = preg_replace('/#([bius])/i',  "<$1>",              $text);
   $text = preg_replace('/#([1-7])/',    "<font size='$1'>",  $text);
   $text = preg_replace('/#([a-z]+)/i',  "<font color='$1'>", $text);
   $text = str_replace( '#-',            "</font>",           $text);

   return $text;
}

function LookupLinks($url, $links) // Recipe 41
{
   // Recipe 41: Lookup Links
   //
   // Note: This initially had the function name of 
   // CheckLinks(), but that clashed with the name of function 23
   // so this one is now called LookupLinks().
   //
   // This recipe takes the URL of a page to check, along
   // with an array of links that should appear at that URL.
   // It returns an array with the value TRUE if all links
   // are in place, otherwise it returns a two element array
   // the first of which is FALSE and the second is an array
   // of all missing links. The arguments required are:
   //
   //    $url:   URL of a web page to check
   //    $links: Array of links to verify

   $results = GetLinksFromURL($url);
   $missing = array();
   $failed  = 0;
   
   foreach($links as $link)
      if (!in_array($link, $results))
         $missing[$failed++] = $link;
         
   if ($failed == 0) return array(TRUE);
   else return array(FALSE, $missing);
}

function GetTitleFromURL($page) // Recipe 42
{
   // Recipe 42: Get Title From URL
   //
   // This recipe takes the URL of a web page and returns that
   // page's title. If the page cannot be loaded then FALSE is
   // returned. The argument required is:
   //
   //    $page: The URL of a page, including the preceding
   //           http://

   $contents = @file_get_contents($page);
   if (!$contents) return FALSE;
   
   preg_match("/<title>(.*)<\/title>/i", $contents, $matches);

   if (count($matches)) return $matches[1];
   else return FALSE;
}

function AutoBackLinks($filename) // Recipe 43
{
   // Recipe 42: Auto Back Links
   //
   // This recipe takes the filename of a log file, as
   // supplied to Recipe 30, RefererLog(), and returns
   // a two element array where the first value is TRUE and
   // the second is an array of inbound links sorted by the
   // amount of hits received. If there are no links only a
   // single element array is returned, with the value FALSE.
   // The argument required is:
   //
   //    $filename: The name of a log file

   if (!file_exists($filename)) return array(FALSE);
   
   $inbound = array();
   $logfile = file_get_contents($filename);
   $links   = explode("\n", rtrim($logfile));
   $links   = array_count_values($links);
   arsort($links, SORT_NUMERIC);
   
   foreach ($links as $key => $val)
      if ($key != " No Referer")
         $inbound[] = $key;

   return array(TRUE, $inbound);
}

function CreateShortURL($url, $redirect, $len, $file) // Recipe 44
{
   // Recipe 44: Create Short URL
   //
   // This recipe takes a long URL and shortens it. The
   // arguments required are:
   //
   //    $url:      A URL including the preceding http://
   //    $redirect: A PHP page on the server to use for URL
   //               redirection, It should have a short
   //               name, such as /go.php.
   //    $len:      The number of characters to use in the
   //               short URL's tail.
   //    $file:     Location of a file containing the data
   //               for the redirects.

   $contents = @file_get_contents($file);
   $lines    = explode("\n", $contents);
   $shorts   = array();
   $longs    = array();

   if (strlen($contents))
      foreach ($lines as $line)
         if (strlen($line))
            list($shorts[], $longs[]) = explode('|', $line);

   if (in_array($url, $longs))
      for ($j = 0 ; $j < count($longs) ; ++$j)
         if ($longs[$j] == $url) return $redirect .
            "?u=" . $shorts[$j];

   do $str = substr(md5(rand(0, 1000000)), 0, $len);
   while (in_array($str, $shorts));
   
   file_put_contents($file, "$contents$str|$url\n");
   return $redirect . "?u=$str";
}

function UseShortURL($token, $file) // Recipe 45
{
   // Recipe 45: Use Short URL
   //
   // This recipe takes a short tail string as created by
   // Recipe 44 and returns the original long URL. The
   // arguments required are:
   //
   //    $token: A short tail as supplied by Recipe 44
   //    $file:  Location of a file containing the data
   //            for the redirects.

   $contents = @file_get_contents($file);
   $lines    = explode("\n", $contents);
   $shorts   = array();
   $longs    = array();

   if (strlen($contents))
      foreach ($lines as $line)
         if (strlen($line))
            list($shorts[], $longs[]) = explode('|', $line);

   if (in_array($token, $shorts))
      for ($j = 0 ; $j < count($longs) ; ++$j)
         if ($shorts[$j] == $token)
            return $longs[$j];

   return FALSE;
}

function SimpleWebProxy($url, $redirect) // Recipe 46
{
   // Recipe 46: Simple Web Proxy
   //
   // This recipe takes a URL as an argument which it then
   // passes back to the web browser with all links changed
   // to keep the proxy working. The arguments required are:
   //
   //    $url:      URL of a page to display
   //    $redirect: Location of the PHP proxy program

   $contents = @file_get_contents($url);
   if (!$contents) return NULL;
   
   switch(strtolower(substr($url, -4)))
   {
      case ".jpg": case ".gif": case ".png": case ".ico":
      case ".css": case ".js": case ".xml":
         return $contents;
   }

   $contents = str_replace('&amp;', '&',         $contents);
   $contents = str_replace('&',     '!!**1**!!', $contents);
   
   $dom      = new domdocument();
   @$dom     ->loadhtml($contents);
   $xpath    = new domxpath($dom);
   $hrefs    = $xpath->evaluate("/html/body//a");
   $sources  = $xpath->evaluate("/html/body//img");
   $iframes  = $xpath->evaluate("/html/body//iframe");
   $scripts  = $xpath->evaluate("/html//script");
   $css      = $xpath->evaluate("/html/head/link");
   $links    = array();

   for ($j = 0 ; $j < $hrefs->length ; ++$j)
      $links[] = $hrefs->item($j)->getAttribute('href');
    
   for ($j = 0 ; $j < $sources->length ; ++$j)
      $links[] = $sources->item($j)->getAttribute('src');

   for ($j = 0 ; $j < $iframes->length ; ++$j)
      $links[] = $iframes->item($j)->getAttribute('src');

   for ($j = 0 ; $j < $scripts->length ; ++$j)
      $links[] = $scripts->item($j)->getAttribute('src');

   for ($j = 0 ; $j < $css->length ; ++$j)
      $links[] = $css->item($j)->getAttribute('href');

   $links = array_unique($links);
   $to    = array();
   $count = 0;
   sort($links);
   
   foreach ($links as $link)
   {
      if ($link != "")
      {
         $temp = str_replace('!!**1**!!', '&', $link);

         $to[$count] = "/$redirect?u=" .
           urlencode(RelToAbsURL($url, $temp));
         $contents = str_replace("href=\"$link\"",
            "href=\"!!$count!!\"", $contents);
         $contents = str_replace("href='$link'",
            "href='!!$count!!'",   $contents);
         $contents = str_replace("href=$link",
            "href=!!$count!!",     $contents);
         $contents = str_replace("src=\"$link\"",
            "src=\"!!$count!!\"",  $contents);
         $contents = str_replace("src='$link'",
            "src='!!$count!!'",    $contents);
         $contents = str_replace("src=$link",
            "src=!!$count!!",      $contents);
         ++$count;
      }
   }

   for ($j = 0 ; $j < $count ; ++$j)
      $contents = str_replace("!!$j!!", $to[$j],
         $contents);

   return str_replace('!!**1**!!', '&', $contents);
}

function PageUpdated($page, $datafile) // Recipe 47
{
   // Recipe 47: Page Updated
   //
   // This recipe takes a URL as an argument which it then
   // checks to see if it is different to the last time it
   // was loaded. If so it returns 1, otherwise it returns 0
   // if the page is unchanged, -1 if the page is new to
   // the data file, or -2 if the page could not be loaded.
   // The arguments required are:
   //
   //    $url:      URL of a page to check
   //    $datafile: File in which to store the database

   $contents = @file_get_contents($page);
   if (!$contents) return FALSE;

   $checksum = md5($contents);

   if (file_exists($datafile))
   {
      $rawfile  = file_get_contents($datafile);
      $data     = explode("\n", rtrim($rawfile));
      $left     = array_map("PU_F1", $data);
      $right    = array_map("PU_F2", $data);
      $exists   = -1;

      for ($j = 0 ; $j < count($left) ; ++$j)
      {
         if ($left[$j] == $page)
         {
            $exists = $j;
            if ($right[$j] == $checksum) return 0;
         }
      }

      if ($exists > -1)
      {
         $rawfile = str_replace($right[$exists],
            $checksum, $rawfile);
         file_put_contents($datafile, $rawfile);
         return 1;
      }
   }
   else $rawfile = "";

   file_put_contents($datafile, "$rawfile$page!1!$checksum\n");
   return -1;
}

function PU_F1($s)
{
   list($a, $b) = explode("!1!", $s);
   return $a;
}

function PU_F2($s)
{
   list($a, $b) = explode("!1!", $s);
   return $b;
}

function HTMLToRSS($html, $title, $description, $url, $webmaster, $copyright) // Recipe 48
{
   // Recipe 48: HTML To RSS
   //
   // This recipe takes a string containing a complete HTML
   // page and turns it into RSS format which is returned. The
   // arguments required are:
   //
   //    $html:        HTML to convert to RSS
   //    $title:       Title to use
   //    $description: Description to use
   //    $url:         URL to link to (generally same as the
   //                  HTML source)
   //    $webmaster:   Webmaster contact email address
   //    $copyright:   Copyright details

   date_default_timezone_set('utc');
   $date  = date("D, d M Y H:i:s e");
   $html  = str_replace('&amp;', '&',         $html);
   $html  = str_replace('&',     '!!**1**!!', $html);
   $dom   = new domdocument();
   @$dom  ->loadhtml($html);
   $xpath = new domxpath($dom);
   $hrefs = $xpath->evaluate("/html/body//a");
   $links = array();
   $to    = array();
   $count = 0;

   for ($j = 0 ; $j < $hrefs->length ; ++$j)
      $links[] = $hrefs->item($j)->getAttribute('href');

   $links = array_unique($links);
   sort($links);

   foreach ($links as $link)
   {
      if ($link != "")
      {
         $temp = str_replace('!!**1**!!', '&', $link);
         $to[$count] = urlencode(RelToAbsURL($url, $temp));
         $html = str_replace("href=\"$link\"",
            "href=\"!!$count!!\"", $html);
         $html = str_replace("href='$link'",
            "href='!!$count!!'",   $html);
         $html = str_replace("href=$link",
            "href=!!$count!!",     $html);
         ++$count;
      }
   }

   for ($j = 0 ; $j < $count ; ++$j)
      $html = str_replace("!!$j!!", $to[$j],
         $html);

   $html = str_replace('http%3A%2F%2F', 'http://', $html);
   $html = str_replace('!!**1**!!', '&', $html);
   $html = preg_replace('/[\s]+/', ' ', $html);
   $html = preg_replace('/<script[^>]*>.*?<\/script>/i', '', $html);
   $html = preg_replace('/<style[^>]*>.*?<\/style>/i', '', $html);
   $ok   = '<a><i><b><u><s><h><img><div><span><table><tr>' .
           '<th><tr><td><br><p><ul><ol><li>';
   $html = strip_tags($html, $ok);
   $html = preg_replace('/<h[1-7][^>]*?>/i', '<h>', $html);
   $html = htmlentities($html);
   $html = preg_replace("/&lt;h&gt;/si",
      "</description></item>\n<item><title>", $html);
   $html = preg_replace("/&lt;\/h[1-7]&gt;/si",
      "</title><guid>$url</guid><description>", $html);
	
	return <<<_END
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"><channel>
<generator>webdeveloperscookbook.com: recipe 48</generator>
<title>$title</title><link>$url</link>
<description>$description</description>
<language>en</language>
<webMaster>$webmaster</webMaster>
<copyright>$copyright</copyright>
<pubDate>$date</pubDate>
<lastBuildDate>$date</lastBuildDate>
<item><title>$title</title>
<guid>$url</guid>
<description>$html</description></item></channel></rss>
_END;
}

function RSSToHTML($rss) // Recipe 49
{
   // Recipe 49: HTML To RSS
   //
   // This recipe takes a string containing an XML RSS
   // feed and returns a string of formatted HTML. The
   // argument required is:
   //
   //    $rss: RSS to convert to HTML

   $xml    = simplexml_load_string($rss);
   $title  = @$xml->channel->title;
   $link   = @$xml->channel->link;
   $desc   = @$xml->channel->description;
   $copyr  = @$xml->channel->copyright;
   $ilink  = @$xml->channel->image->link;
   $ititle = @$xml->channel->image->title;
   $iurl   = @$xml->channel->image->url;

   $out = "<html><head><style> img {border: 1px solid " .
          "#444444}</style>\n<body>";

   if ($ilink != "")
      $out    .= "<a href='$ilink'><img src='$iurl' title=" .
                 "'$ititle' alt='$ititle' border='0' style=" .
                 "'border: 0px' align='left' /></a>\n";
   
   $out .= "<h1>$title</h1>\n<h2>$desc</h2>\n";
   
   foreach($xml->channel->item as $item)
   {
      $tlink  = @$item->link;
      $tdate  = @$item->pubDate;
      $ttitle = @$item->title;
      $tdesc  = @$item->description;
      
      $out   .= "<h3><a href='$tlink' title='$tdate'>" .
                "$ttitle</a></h3>\n<p>$tdesc</p>\n";
   }

   return "$out<a href='$link'>$copyr</a></body></html>";
}

function HTMLToMobile($html, $url, $style, $images) // Recipe 50
{
   // Recipe 50: HTML To Mobile
   //
   // This recipe takes a string containing a complete HTML
   // page and turns it into a format more quickly and clearly
   // displayed by a mobile browser. The arguments required are:
   //
   //    $html:   HTML to convert to Mobile Browser
   //    $url:    URL of page being converted
   //    $style:  If "yes" keep style & JavaScript
   //    $images: If "yes" keep images

   $dom   = new domdocument();
   @$dom  ->loadhtml($html);
   $xpath = new domxpath($dom);
   $hrefs = $xpath->evaluate("/html/body//a");
   $links = array();
   $to    = array();
   $count = 0;
   $html  = str_replace('&amp;', '&',         $html);
   $html  = str_replace('&',     '!!**1**!!', $html);

   for ($j = 0 ; $j < $hrefs->length ; ++$j)
      $links[] = $hrefs->item($j)->getAttribute('href');

   $links = array_unique($links);
   sort($links);

   foreach ($links as $link)
   {
      if ($link != "")
      {
         $temp = str_replace('!!**1**!!', '&', $link);
         $to[$count] = urlencode(RelToAbsURL($url, $temp));
         $html = str_replace("href=\"$link\"",
            "href=\"!!$count!!\"", $html);
         $html = str_replace("href='$link'",
            "href='!!$count!!'",   $html);
         $html = str_replace("href=$link",
            "href=!!$count!!",     $html);
         ++$count;
      }
   }

   for ($j = 0 ; $j < $count ; ++$j)
      $html = str_replace("!!$j!!", $to[$j], $html);

   $html = str_replace('http%3A%2F%2F', 'http://', $html);
   $html = str_replace('!!**1**!!', '&', $html);

   if (strtolower($style) != "yes")
   {
      $html = preg_replace('/[\s]+/', ' ', $html);
      $html = preg_replace('/<script[^>]*>.*?<\/script>/i', '', $html);
      $html = preg_replace('/<style[^>]*>.*?<\/style>/i', '', $html);
   }

   $allowed = "<a><p><h><i><b><u><s>";
   if (strtolower($images) == "yes") $allowed .= "<img>";
   return strip_tags($html, $allowed);
}

function UsersOnline($datafile, $seconds) // Recipe 51
{
   // Recipe 51: Users Online
   //
   // This recipe takes the name of a datafile and the
   // number of seconds between accesses to assume a user is
   // still online and returns the number of users who fit
   // those requirements. The arguments required are:
   //
   //    $datafile: Database file for user information
   //    $seconds:  No. of seconds since last access to consider
   //               a user still online

   $ip     = getenv("REMOTE_ADDR") .
             getenv("HTTP_USER_AGENT");
   $out    = "";
   $online = 1;

   if (file_exists($datafile))
   {
      $users = explode("\n", rtrim(file_get_contents($datafile)));

      foreach($users as $user)
      {
         list($usertime, $userip) = explode('|', $user);

         if ((time() - $usertime) < $seconds && $userip != $ip)
         {
            $out .= $usertime . '|' . $userip . "\n";
            ++$online;
         }
      }
   }

   $out .= time() . '|' . $ip . "\n";
   file_put_contents($datafile, $out);
   return $online;
}

function PostToGuestBook($datafile, $name, $email, $website, $message) // Recipe 52
{
   // Recipe 52: Post To Guest Book
   //
   // This recipe takes the name of a datafile in which a
   // guestbook is stored and adds a post to it. The
   // arguments required are:
   //
   //    $datafile: File in which to store guestbook data
   //    $name:     User's name
   //    $email:    Their email address
   //    $website:  Their website
   //    $message:  Their message
   //
   // The recipe returns 1 if the message has been posted,
   // 0 if the message was already posted and therefore has
   // been ignored, or -1 if the file could not be written to.

   $data = "$name!1!$email!1!$website!1!$message";

   if (file_exists($datafile))
   {
      $lines = explode("\n", rtrim(file_get_contents($datafile)));

      if (in_array($data, $lines)) return 0;
   }

   $fh = fopen($datafile, 'a');
   if (!$fh) return -1;

   if (flock($fh, LOCK_EX)) fwrite($fh, $data . "\n");
   flock($fh, LOCK_UN);
   fclose($fh);
   return 1;
}

function GetGuestBook($datafile, $order) // Recipe 53
{
   // Recipe 53: Get Guest Book
   //
   // This recipe takes the name of a guestbook data file
   // and returns the posts in the guestbook. The arguments
   // required are:
   //
   //    $datafile:  File in which to store guestbook data
   //    $order:     If "r" then the order of the returned
   //                posts is reversed, so that the most
   //                recent post is returned first.
   //
   // If there are any posts the recipe returns a two
   // element array, the first of which is the number of
   // posts, and the second is a two dimensional array:
   //
   //    array[0][0] Name from the first post
   //    array[0][1] Email from the first post
   //    array[0][2] Website from the first post
   //    array[0][3] Message from the first post
   //    array[1][1] Name from the second post etc...
   //
   // If there are no posts then a single element array is
   // returned with the value 0.
   
   if (!file_exists($datafile)) return array(0);

   $data  = array();
   $posts = explode("\n", rtrim(file_get_contents($datafile)));

   if (strtolower($order) == 'r')
      $posts = array_reverse($posts);

   foreach ($posts as $post)
      $data[] = explode('!1!', $post);

   return array(count($posts), $data);
}

function PostToChat($datafile, $maxposts, $maxlength, $from, $to, $message, $floodctrl) // Recipe 54
{
   // Recipe 54: Post To Chat
   //
   // This recipe takes the name of a chatroom data file
   // and posts a message to it. If $max messages already
   // exist the oldest one is removed to create room. The
   // arguments required are:
   //
   //    $datafile:  File in which to store chatroom data
   //    $maxposts   Maximum number of old messages to retain
   //    $maxlength: Maximum length of message
   //    $from:      Username of message sender
   //    $to:        Username of message recipient. If blank
   //                the message is public and to everyone in
   //                the room, otherwise it is private and
   //                only the sender and recipient see it.
   //    $message:   The message to post
   //    $floodctrl: If "on" do not allow the same message
   //                from the same user to be reposted
   //
   // Upon success this recipe returns 1, otherwise it
   // returns -1 if the message could not be posted because
   // the file could not be written to, or 0 if flooding
   // control prevented the message being posted. If the
   // message is empty or either of $from or $to contain
   // a | character (disallowed) then -2 is returned.

   if (!file_exists($datafile))
   {
      $data = "";
      for ($j = 0 ; $j < $maxposts ; ++$j) $data .= "$j|||\n";
      file_put_contents($datafile, $data);
   }

   if ($message == "" || strpos($from, '|') ||
      strpos($to, '|')) return -2;

   $message = str_replace('|',  '&#124;', $message);
   $message = substr($message, 0, $maxlength);
   $fh      = fopen($datafile, 'r+');
   if (!$fh) return -1;

   flock($fh, LOCK_EX);
   fgets($fh);
   $text = fread($fh, 100000);

   if (strtolower($floodctrl) == 'on' &&
      strpos($text, "|$to|$from|$message\n"))
   {
      flock($fh, LOCK_UN);
      fclose($fh);
      return 0;
   }

   $lines = explode("\n", $text);
   $temp  = explode('|', $lines[$maxposts - 2]);
   $text .= ($temp[0] + 1) . "|$to|$from|$message\n";
   fseek($fh, 0);
   fwrite($fh, $text);
   ftruncate($fh, strlen($text));
   flock($fh, LOCK_UN);
   fclose($fh);
   return 1;
}

function ViewChat($datafile, $username, $maxtime) // Recipe 55
{
   // Recipe 55: View Chat
   //
   // This recipe takes the name of a chatroom data file
   // and displays the posts in it. The arguments required
   // are:
   //
   //    $datafile: File containing chat data
   //    $username: The username of the chat viewer
   //    $maxtime:  Maximum number of seconds the script
   //               may run for before returning
   //
   // Upon failure this recipe returns FALSE, otherwise it
   // will return TRUE.

   if (!file_exists($datafile)) return FALSE;

   set_time_limit($maxtime + 5);
   $tn      = time();
   $tstart  = "<table width='100%' border='0'><tr><td " .
              "width='15%' align='right'>";
   $tmiddle = "</td><td width='85%'>";
   $tend    = "</td></tr></table><script>scrollBy(0,1000);" .
              "</script>\n";
   $oldpnum = 0;
   
   while (1)
   {
      $lines = explode("\n", rtrim(file_get_contents($datafile)));

      foreach ($lines as $line)
      {
         $thisline = explode("|", $line);
         $postnum  = $thisline[0];
         $to       = $thisline[1];
         $from     = $thisline[2];
         $message  = $thisline[3];

         if ($postnum > $oldpnum)
         {
            if ($to == "")
            {
               echo "$tstart$from:$tmiddle$message$tend";
            }
            elseif ($to == $username || $from == $username)
            {
               echo "$tstart$from:$tmiddle(PM to $to) " .
			   "<i>$message</i>$tend";
            }

            $oldpnum = $postnum;
            ob_flush();
            flush();
         }
      }
      
      sleep(2);
      if ((time() - $tn) > $maxtime) return TRUE;
   }
}

function SendTweet($user, $pass, $text) // Recipe 56
{
   // Recipe 56 Send Tweet
   //
   // This recipe sends a tweet to a Twitter account. The
   // arguments required are:
   //
   //    $user: Twitter username
   //    $pass: Twitter password
   //    $text: Text to Tweet
   //
   // Upon success this recipe returns the XML text
   // returned by the called web page, otherwise it will
   // return FALSE.

   $text = substr($text, 0, 140);
   $url  = 'http://twitter.com/statuses/update.xml';
   $curl_handle = curl_init();
   curl_setopt($curl_handle, CURLOPT_URL, "$url");
   curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
   curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl_handle, CURLOPT_POST, 1);
   curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "status=$text");
   curl_setopt($curl_handle, CURLOPT_USERPWD, "$user:$pass");
   $result = curl_exec($curl_handle);
   curl_close($curl_handle);

   $xml = simplexml_load_string($result);
   if     ($xml == FALSE)       return FALSE;
   elseif ($xml->text == $text) return TRUE;
   else                         return FALSE;
}

function SendDirectTweet($user, $pass, $to, $text) // Recipe 57
{
   // Recipe 57 Send Direct Tweet
   //
   // This recipe sends a Direct Message to a Twitter
   // account. The arguments required are:
   //
   //    $user: Twitter username
   //    $pass: Twitter password
   //    $to:   Recipient of Direct Tweet
   //    $text: Text to Tweet
   //
   // Upon success this recipe returns TRUE, otherwise it
   // will return FALSE.

   $text = substr($text, 0, 140);
   $url  = 'http://twitter.com/direct_messages/new.xml';
   $curl_handle = curl_init();
   curl_setopt($curl_handle, CURLOPT_URL, "$url");
   curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
   curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl_handle, CURLOPT_POST, 1);
   curl_setopt($curl_handle, CURLOPT_POSTFIELDS,
      "user=$to&text=$text");
   curl_setopt($curl_handle, CURLOPT_USERPWD, "$user:$pass");
   $result = curl_exec($curl_handle);
   curl_close($curl_handle);

   $xml = simplexml_load_string($result);
   if     ($xml == FALSE)       return FALSE;
   elseif ($xml->text == $text) return TRUE;
   else                         return FALSE;
}

function GetTweets($user) // Recipe 58
{
   // Recipe 58 Get Tweets
   //
   // This recipe returns the most recent 20 tweets of a Twitter
   // user. The argument required is:
   //
   //    $user: Twitter username
   //
   // Upon success the recipe returns a two element array, the
   // first of which contains the number of tweets returned, and
   // the second is an array of the tweets. On failure a single
   // element array is returned with the value FALSE.

   date_default_timezone_set('utc');
   $url  = "http://twitter.com/statuses/user_timeline/$user.xml";
   $file = @file_get_contents($url);
   if (!strlen($file)) return array(FALSE);
   
   $xml  = @simplexml_load_string($file);
   if ($xml == FALSE) return array(FALSE);
   
   $tweets = array();

   foreach ($xml->status as $tweet)
   {
      $timestamp = strtotime($tweet->created_at);
      $tweets[] = "(" . date("M jS, g:ia", $timestamp) . ") " .
         $tweet->text;
   }

   return array(count($tweets), $tweets);
}

function ReplaceSmileys($text, $folder) // Recipe 59
{
   // Recipe 59 Replace Smileys
   //
   // This recipe replaces any smiley emoticons in a string
   // with HTML refrring to animated GIFs. The arguments
   // required are:
   //
   //    $text:   The text to process
   //    $folder: Folder containing the smiley GIFs
   //
   // The Download link on the website includes a folder of
   // 20 smiley GIFs which will work with this recipe, as
   // long as you do not rename them.

   $chars = array('>:-(', '>:(', 'X-(',  'X(',
                  ':-)*', ':)*', ':-*',  ':*', '=*',
                  ':)',   ':]',
                  ':-)',  ':-]',
                  ':(',   ':C',   ':[',
                  ':-(',  ':\'(', ':_(',
                  ':O',   ':-O',
                  ':P',   ':b',   ':-P', ':-b',
                  ':D',   'XD',
                  ';)',   ';-)',
                  ':/',   ':\\',  ':-/', ':-\\',
                  ':|',
                  'B-)',  'B)',
                  'I-)',  'I)',
                  ':->',  ':>',
                  ':X',   ':-X',
                  '8)',   '8-)',
                  '=-O',  '=O',
                  'O.o',  ':S',   ':-S',
                  '*-*',  '*_*');

   $gifs = array( 'angry',   'angry',   'angry',  'angry',
                  'kiss',    'kiss',    'kiss',   'kiss', 'kiss',
                  'smiley',  'smiley',
                  'happy',   'happy',
                  'sad',     'sad',     'sad',
                  'cry',     'cry',     'cry',
                  'shocked', 'shocked',
                  'tongue',  'tongue',  'tongue', 'tongue',
                  'laugh',   'laugh',
                  'wink',    'wink',
                  'uneasy',  'uneasy',  'uneasy', 'uneasy',
                  'blank',
                  'cool',    'cool',
                  'sleep',   'sleep',
                  'sneaky',  'sneaky',
                  'blush',   'blush',
                  'wideeye', 'wideeye',
                  'uhoh',    'uhoh',
                  'puzzled', 'puzzled', 'puzzled',
                  'dizzy',   'dizzy');

   if (substr($folder, -1) == '/')
      $folder = substr($folder, 0, -1);

   for ($j = 0 ; $j < count($gifs) ; ++$j)
      $gifs[$j] = "<image src='$folder/$gifs[$j].gif' " .
         "width='15' height='15' border='0' alt='$gifs[$j]' " .
         "title='$gifs[$j]' />";

   return str_ireplace($chars, $gifs, $text);
}

function ReplaceSMSTalk($text) // Recipe 60
{
   // Recipe 60 Replace SMS Talk
   //
   // This recipe replaces any SMS Text Speak acronyms
   // with standard English equivalents. The argument
   // required is:
   //
   //    $text: The text to process

   $sms = array('ABT2', 'about to',
                'AFAIC', 'as far as I\'m concerned',
                'AFAIK', 'as far as I know',
                'AML', 'all my love',
                'ATST', 'at the same time',
                'AWOL', 'absent without leave',
                'AYK', 'as you know',
                'AYTMTB', 'and you\'re telling me this because?',
                'B4', 'before',
                'B4N', 'bye for now',
                'BBT', 'be back tomorrow',
                'BRB', 'be right back',
                'BTW', 'by the way',
                'BW', 'best wishes',
                'BYKT', 'but you knew that',
                'CID', 'consider it done',
                'CSL', 'can\'t stop laughing',
                'CYL', 'see you later',
                'CYT', 'see you tomorrow',
                'DGA ', 'don\'t go anywhere',
                'DIKU', 'do I know you?',
                'DLTM', 'don\'t lie to me',
                'FF', 'friends forever',
                'FYI', 'for your information',
                'GBH', 'great big hug',
                'GG', 'good game',
                'GL', 'good luck',
                'GR8', 'great',
                'GTG', 'got to go',
                'HAK', 'hugs and kisses',
                'ILU', 'I love you',
                'IM', 'instant message',
                'IMHO', 'in my humble opinion',
                'IMO', 'in my opinion',
                'IMS', 'I\'m sorry',
                'IOH', 'I\'m outta here',
                'JK', 'just kidding',
                'KISS', 'Keep it simple silly',
                'L8R', 'later',
                'LOL', 'laughing out loud',
                'M8 ', 'mate',
                'MSG', 'message',
                'N1', 'nice one',
                'NE1', 'anyone?',
                'NMP', 'not my problem',
                'NOYB', 'none of your business',
                'NP', 'no problem',
                'OMDB', 'over my dead body',
                'OMG', 'oh my gosh',
                'ONNA', 'oh no, not again',
                'OOTO', 'out of the office',
                'OT', 'off topic',
                'OTT', 'over the top',
                'PLS', 'please',
                'PM', 'personal message',
                'POOF', 'goodbye',
                'QL', 'quit laughing',
                'QT', 'cutie',
                'RBTL ', 'reading between the lines',
                'ROLF', 'rolling on the floor laughing',
                'SMEM', 'send me an email',
                'SMIM', 'send me an instant message',
                'SO', 'significant other',
                'SOHF', 'sense of humor failure',
                'STR8', 'straight',
                'SYS', 'see you soon',
                'TAH', 'take a hike',
                'TBC', 'to be continued',
                'TFH', 'thread from hell',
                'TGIF', 'thank goodness it\'s Friday',
                'THX', 'thanks',
                'TM', 'trust me',
                'TOM', 'tomorrow',
                'TTG', 'time to go',
                'TVM', 'thank you very much',
                'VM', 'voice mail',
                'WC', 'who cares?',
                'WFM', 'Works for me',
                'WTG', 'way to go',
                'WYP', 'what\'s your problem?',
                'WYWH', 'wish you were here',
                'XOXO', 'hugs and kisses',
                'ZZZ', 'sleeping, bored');

   $from1 = array(); $from2 = array();
   $to1   = array(); $to2   = array();
   
   for ($j = 0 ; $j < count($sms) ; $j += 2)
   {
      $from1[$j] = "/\b$sms[$j]\b/";
      $to1[$j]   = ucfirst($sms[$j + 1]);

      $from2[$j] = "/\b$sms[$j]\b/i";
      $to2[$j]   = $sms[$j + 1];
   }

   $text = preg_replace($from1, $to1, $text);
   return  preg_replace($from2, $to2, $text);
}

function AddUserToDB($table, $nmax, $hmax, $salt1, $salt2, $name, $handle, $pass, $email) // Recipe 61
{
   // Recipe 61 Add User To DB
   //
   // This recipe adds a user to the selected table
   // and database. If the table doesn't exist it
   // is created. It takes these arguments:
   //
   //    $table:  The table name within $dbname
   //    $nmax:   The max length of $name
   //    $hmax:   The max length of $handle
   //    $salt1:  Characters to obscure $pass
   //    $salt2:  More obscuring characters for $pass
   //    $name:   The user's real name to add to table
   //    $handle: The user's handle or username
   //    $pass:   The password for $handle
   //    $email:  The email address of $handle

   $query = "CREATE TABLE IF NOT EXISTS $table(" .
            "name VARCHAR($nmax), handle VARCHAR($hmax), " .
            "pass CHAR(32), email VARCHAR(256), " .
            "INDEX(name(6)), INDEX(handle(6)), " .
            "INDEX(email(6)))";
   mysql_query($query) or die(mysql_error());

   $query = "SELECT * FROM $table WHERE handle='$handle'";
   if (mysql_num_rows(mysql_query($query)) == 1) return -2;

   $pass  = md5($salt1 . $pass . $salt2);
   $query = "INSERT INTO $table VALUES('$name', '$handle', " .
            "'$pass', '$email')";
   if (mysql_query($query)) return 1;
   else return -1;
}

function GetUserFromDB($table, $handle) // Recipe 62
{
   // Recipe 62 Get User From DB
   //
   // This recipe accepts a username in $handle and then
   // returns all details (if any) held for that handle.
   // On success it returns a two element array with the
   // first element being TRUE and the second the array of
   // user details. On failure it returns a single element
   // array with the value FALSE. It expects these
   // arguments:
   //
   //    $table:  The table name within $dbname
   //    $handle: The user's handle or username

   $query  = "SELECT * FROM $table WHERE handle='$handle'";
   $result = mysql_query($query);
   if (mysql_num_rows($result) == 0) return array(FALSE);
   else return array(TRUE, mysql_fetch_array($result, MYSQL_NUM));
}

function VerifyUserInDB($table, $salt1, $salt2, $handle, $pass) // Recipe 63
{
   // Recipe 63 Verify User In DB
   //
   // This recipe accepts a handle and password and then
   // verifies whether they are correct. It expects these
   // arguments:
   //
   //    $table:  The table name within $dbname
   //    $salt1:  A salt as used when adding a user
   //    $salt2:  The second salt used
   //    $handle: The handle as input by a user
   //    $pass:   The password entered by the user

   $result = GetUserFromDB($table, $handle);
   if ($result[0] == FALSE) return FALSE;
   elseif ($result[1][2] == md5($salt1 . $pass . $salt2))
      return TRUE;
   else return FALSE;
}

function SanitizeString($string) // Recipe 64
{
   // Recipe 64a Sanitize String
   //
   // This recipe accepts a string, which then has any
   // potentially malicious characters removed from it.
   // It expects this argument:
   //
   //    $string: The string to sanitize

	$string = strip_tags($string);
	return htmlentities($string);
}

function MySQLSanitizeString($string)
{
   // Recipe 64b MySQL Sanitize String
   //
   // This recipe accepts a string, which then has any
   // potentially malicious characters removed from it.
   // This includes any characters that could be used to
   // try and compromise a MySQL database. Only call
   // this once a connection has been opened to a MySQL
   // database, otherwise an error will occur. It expects
   // this argument:
   //
   //    $string: The string to sanitize

   if (get_magic_quotes_gpc())
      $string = stripslashes($string);
   $string = SanitizeString($string);
   return mysql_real_escape_string($string);
}

function CreateSession($handle, $pass, $name, $email) // Recipe 65
{
   // Recipe 65 Create Session
   //
   // This recipe starts a PHP session, assigning the
   // four user details as session variables so that no
   // further database lookups or logins are required.
   // On success it returns TRUE, otherwise FALSE.
   // It takes these arguments:
   //
   //    $handle: User handle
   //    $pass:   User password
   //    $name:   User' name
   //    $email:  User's email address

   if (!session_start()) return FALSE;

   $_SESSION['handle'] = $handle;
   $_SESSION['pass']   = $pass;
   $_SESSION['name']   = $name;
   $_SESSION['email']  = $email;
   $_SESSION['ipnum']  = getenv("REMOTE_ADDR");
   $_SESSION['agent']  = getenv("HTTP_USER_AGENT");

   return TRUE;
}

function OpenSession() // Recipe 66
{
   // Recipe 66 Open Session
   //
   // This recipe returns the four user variables.
   // It doesn't take any parameters. On success it
   // returns a two-element array, the first of which
   // has the value FALSE, and the second is an array
   // of values. On failure (if the session variables
   // don't exists, for example), it returns a single
   // element array with the value FALSE. An easy way
   // to read the return values is with a list()
   // statement, like this:
   //
   //    $result = ReadSession();
   //    list($h, $p, $n, $e) = $result[1];

   if (!@session_start()) return array(FALSE);
   if (!isset($_SESSION['handle'])) return array(FALSE);

   $vars = array();
   $vars[] = $_SESSION['handle'];
   $vars[] = $_SESSION['pass'];
   $vars[] = $_SESSION['name'];
   $vars[] = $_SESSION['email'];
   return array(TRUE, $vars);
}

function CloseSession() // Recipe 67
{
   // Recipe 67 Close Session
   //
   // This recipe ends a previously started session.
   // It does not take any arguments and returns TRUE
   // on success, otherwise FALSE.

	$_SESSION = array();

	if (session_id() != "" ||
       isset($_COOKIE[session_name()]))
	   setcookie(session_name(), '', time() - 2592000, '/');

	return @session_destroy();
}

function SecureSession() // Recipe 68
{
   // Recipe 68 Secure Session
   //
   // This recipe tests whether the IP address or User
   // Agent are different from those of the user who
   // initiated the session. If so, it terminates the
   // session to prevent hijacking. It returns TRUE if
   // the session appears secure, otherwise it closes
   // any session that appears insecure and returns
   // FALSE. If the session doesn't exists it returns
   // FALSE. It doesn't take any arguments.
   
   $ipnum = getenv("REMOTE_ADDR");
   $agent = getenv("HTTP_USER_AGENT");

   if (isset($_SESSION['ipnum']))
   {
      if ($ipnum != $_SESSION['ipnum'] ||
          $agent != $_SESSION['agent'])
      {
         CloseSession();
         return FALSE;
      }
      else return TRUE;
   }
   else return FALSE;
}

function ManageCookie($action, $cookie, $value, $expire, $path) // Recipe 69
{
   // Recipe 69 Manage Cookie
   //
   // This recipe provides three ways of interacting with
   // cookies. It must be called before any HTML is sent.
   // Upon success with a 'set' or 'delete' the recipe returns
   // TRUE. For a successful 'read' it returns the read value.
   // On failure it returns FALSE. It requires the following
   // arguments:
   //
   //    $action: If 'set' then set $cookie to $value
   //             If 'read' return the value of $cookie
   //             If 'delete' delete $cookie
   //    $cookie: Name of a cookie to set/read/delete
   //    $value:  If setting a cookie use this value: any string
   //    $expire: If setting a cookie use this value: number
   //             of seconds before cookie expires, or use
   //             NULL to let cookie expire at browser session
   //             end
   //    $path:   The path to the cookie on the server:
   //             Generally this will be '/'

   switch(strtolower($action))
   {
      case 'set':
         if ($expire) $expire += time();
         return setcookie($cookie, $value, $expire, $path);

      case 'read':
         if (isset($_COOKIE[$cookie]))
            return $_COOKIE[$cookie];
         else return FALSE;

      case 'delete':
         if (isset($_COOKIE[$cookie]))
            return setcookie($cookie, NULL,
               time() - 60 * 60 * 24 * 30, NULL);
         else return FALSE;
   }
   
   return FALSE;
}

function BlockUserByCookie($action, $handle, $expire) // Recipe 70
{
   // Recipe 69 Block User By Cookie
   //
   // This recipe either blocks a user or reports on a user's
   // block status. It requires the following arguments:
   //
   //    $action: If 'block' set the user's status to blocked,
   //             otherwise return the user's block status
   //    $handle: If setting a cookie use this value
   //    $expire: If setting a cookie use this value

   if (strtolower($action) == 'block')
   {
      if ($_SESSION['handle'] != $handle) return FALSE;
      else return manageCookie('set', 'user', $handle,
         $expire, '/');
   }

   return manageCookie('read', 'user', NULL, NULL, NULL);
}

function CreateGoogleChart($title, $tcolor, $tsize, $type, $bwidth, $labels, $legends, $colors, $bgfill, $border, $bcolor, $width, $height, $data) // Recipe 71
{
   // Recipe 71: Create Google Chart
   //
   // This recipe returns a GD image created using the Google
   // Charts API. It requires the following arguments where
   // those prefaced by (*) can be set to NULL or '' to use
   // default values:
   //
   //    $title:   (*)The title text
   //    $tcolor:  (*)The title color (6 hex digits)
   //    $tsize:   (*)The title font size
   //    $type:    (*)The chart type, out of: line, vbar, hbar,
   //                 gometer, pie, pie3d, venn and radar
   //    $bwidth:  (*)The width of bars in pixels, if bar chart
   //    $labels:  (*)Data labels, separated by | symbols
   //    $legends: (*)Data legends, separated by | symbols
   //    $colors:  (*)Data colors, separated by | symbols
   //    $bgfill:  (*)Background fill color (6 hex digits)
   //    $border:  (*)Border width in pixels
   //    $bcolor:  (*)Border color (6 hex digits)
   //    $width:   The chart width in pixels
   //    $height:  The chart height in pixels
   //    $data:    The data set, separated by commas

   $types = array('line'    => 'lc',
                  'vbar'    => 'bvg',
                  'hbar'    => 'bhg',
                  'gometer' => 'gom',
                  'pie'     => 'p',
                  'pie3d'   => 'p3',
                  'venn'    => 'v',
                  'radar'   => 'r');

   if (!isset($types[$type])) $type = 'pie';

   $tail  = "chtt=" . urlencode($title);
   $tail .= "&cht=$types[$type]";
   $tail .= "&chs=$width" . "x" . "$height";
   $tail .= "&chbh=$bwidth";
   $tail .= "&chxt=x,y";
   $tail .= "&chd=t:$data";

   if ($tcolor)
      if ($tsize) $tail .= "&chts=$tcolor,$tsize";
   if ($labels)   $tail .= "&chl=$labels";
   if ($legends)  $tail .= "&chdl=$legends";
   if ($colors)   $tail .= "&chco=$colors";
   if ($bgfill)   $tail .= "&chf=bg,s,$bgfill";

   $url   = "http://chart.apis.google.com/chart?$tail";

   // Uncomment the line below to return a URL to the chart image
   // return $url;

   $image = imagecreatefrompng($url);

   $w = imagesx($image);
   $h = imagesy($image);
   $image2 = imagecreatetruecolor($w + $border * 2,
      $h + $border * 2);
   $clr = imagecolorallocate($image,
      hexdec(substr($bcolor, 0, 2)),
      hexdec(substr($bcolor, 2, 2)),
      hexdec(substr($bcolor, 4, 2)));
   imagefilledrectangle($image2, 0, 0, $w + $border * 2,
      $h + $border * 2, $clr);
   imagecopy($image2, $image, $border, $border, 0, 0, $w, $h);
   imagedestroy($image);
   return $image2;
}

function CurlGetContents($url, $agent) // Recipe 72
{
   // Recipe 72: Curl Get Contents
   //
   // This recipe fetches a page that may otherwise be
   // forbidden using the file_get_contents() function.
   // It requires the following arguments:
   //
   //    $url:   The URL of the page to fetch
   //    $agent: A typical browser User Agent string

   $agent = ($agent != '') ? $agent :
      'Mozilla/5.0 (compatible; MSIE 9.0; Windows' .
      ' NT 6.1; Win64; x64; Trident/5.0)';

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL,            $url);
   curl_setopt($ch, CURLOPT_USERAGENT,      $agent);
   curl_setopt($ch, CURLOPT_HEADER,         0);
   curl_setopt($ch, CURLOPT_ENCODING,       "gzip");
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_FAILONERROR,    1);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
   curl_setopt($ch, CURLOPT_TIMEOUT,        8);
   $result = curl_exec($ch);
   curl_close($ch);
   return $result;
}

function FetchWikiPage($entry) // Recipe 73
{
   // Recipe 73: Fetch Wiki Page
   //
   // This recipe fetches the XML of a Wikipedia entry for the
   // term $entry and returns a string containing the salient
   // details. It requires the following argument:
   //
   //    $entry: The entry to fetch (eg: 'bread')

   $agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows' .
            ' NT 6.1; Win64; x64; Trident/5.0)';
   $text  = '';

   while ($text == '' || substr($text, 0, 9) == '#REDIRECT')
   {
      $entry = rawurlencode($entry);
      $url   = "http://en.wikipedia.org/wiki/Special:Export/$entry";
      $page  = CurlGetContents($url, $agent);
      $xml   = simplexml_load_string($page);
      $title = $xml->page->title;
      $text  = $xml->page->revision->text;

      if (substr($text, 0, 9) == '#REDIRECT')
      {
         preg_match('/\[\[(.+)\]\]/', $text, $matches);
         $entry = $matches[1];
      }
   }

   $sections = array('References', 'See also', 'External links',
      'Notes', 'Further reading');

   foreach($sections as $section)
   {
      $ptr = stripos($text, "==$section==");
      if ($ptr) $text = substr($text, 0, $ptr);
      $ptr = stripos($text, "== $section ==");
      if ($ptr) $text = substr($text, 0, $ptr);
   }

   $data = array('\[{2}Imag(\[{2})*.*(\]{2})*\]{2}', '',
                 '\[{2}File(\[{2})*.*(\]{2})*\]{2}', '',
                 '\[{2}Cate(\[{2})*.*(\]{2})*\]{2}', '',
                 '\{{2}([^\{\}]+|(?R))*\}{2}',       '',
                 '\'{3}(.*?)\'{3}',         '<b>$1</b>',
                 '\'{2}(.*?)\'{2}',         '<i>$1</i>',
                 '\[{2}[^\|\]]+\|([^\]]*)\]{2}',   '$1',
                 '\[{2}(.*?)\]{2}',                '$1',
                 '\[(http[^\]]+)\]',                ' ',
                 '\n(\*|#)+',   '<br />&nbsp;&#x25cf; ',
                 '\n:.*?\n',                         '', 
                 '\n\{[^\}]+\}',                     '',
                 '\n={7}([^=]+)={7}',     '<h7>$1</h7>',
                 '\n={6}([^=]+)={6}',     '<h6>$1</h6>',
                 '\n={5}([^=]+)={5}',     '<h5>$1</h5>',
                 '\n={4}([^=]+)={4}',     '<h4>$1</h4>',
                 '\n={3}([^=]+)={3}',     '<h3>$1</h3>',
                 '\n={2}([^=]+)={2}',     '<h2>$1</h2>',
                 '\n={1}([^=]+)={1}',     '<h1>$1</h1>',
                 '\n{2}',                         '<p>',
                 '<gallery>([^<]+?)<\/gallery>',     '',
                 '<ref>([^<]+?)<\/ref>',             '',
                 '<ref [^>]+>',                      '');

   for ($j = 0 ; $j < count($data) ; $j += 2)
      $text = preg_replace("/$data[$j]/", $data[$j+1], $text);

   $text  = strip_tags($text, '<h1><h2><h3><h4><h5><h6><h7>' .
                              '<p><br><b><i>');
   $url   = "http://en.wikipedia.org/wiki/$title";
   $text .= "<p>Source: <a href='$url'>Wikipedia ($title)</a>";
   return trim($text);
}

function FetchFlickrStream($account) // Recipe 74
{
   // Recipe 74: Fetch Flickr Stream
   //
   // This recipe fetches a steam of photo URLs from a Flickr
   // account. Upon success it returns a two element array,
   // the first of which is the number of photos returned and
   // the second is an array containing URLs to each photo. On
   // failure it returns a single element array with the value
   // FALSE. It requires the following argument:
   //
   //    $account A fickr account name

   $url  = 'http://flickr.com/photos';
   $page = @file_get_contents("$url/$account/");
   if (!$page) return array(FALSE);

   $pics = array();
   $rss  = strstr($page, 'rss+xml');
   $rss  = strstr($rss, 'http://');
   $rss  = substr($rss, 0, strpos($rss, '"'));
   $xml  = file_get_contents($rss);
   $sxml = simplexml_load_string($xml);

   foreach($sxml->entry as $item)
   {
      for ($j=0 ; $j < sizeof($item->link) ; ++$j)
      {
         if (strstr($item->link[$j]['type'], 'image'))
         {
            $t=str_replace('_m', '', $item->link[$j]['href']);
            $t=str_replace('_t', '', $t);
            $pics[]=$t;
         }
      }
   }
   
   return array(count($pics), $pics);
}

function GetYahooAnswers($search) // Recipe 75
{
   // Recipe 75: Get Yahoo! Answers
   //
   // This recipe fetches a collection of questions and answers
   // from Yahoo! Answers based on the query passed in $search.
   // Upon success it returns a two element array with the first
   // value being the number of Q&As returned and the second
   // an array containing the sets of Q&As. This array contains
   // sub-arrays for the elements of each Q&A with these items:
   // 1) Subject, 2) Timestamp, 3) Question, 4) Answer, 5) URL.
   // Yahoo! Web Search web services are limited to 5,000 queries
   // per IP per day, per API so you are recommended to cache
   // results where you can. It requires the following argument:
   //
   //    $search: A search query

   // IMPORTANT - Replace $id with your own API key obtained at
   //             http://developer.yahoo.com because this key will
   //             not work as it stands.

   $id     = 'Important-PutYourOwnYahoo!APIKeyHere-OrThisWillNotWork';

   $search = rawurlencode($search);
   $url    = 'http://answers.yahooapis.com' .
             '/AnswersService/V1/questionSearch' .
             "?appid=$id&query=$search";

   $xml    = @file_get_contents($url);
   if (!$xml) return array(FALSE);

   $sxml   = simplexml_load_string($xml);
   $qandas = array();

   foreach($sxml->Question as $question)
   {
      $s = trim($question->Subject);
      $t = $question->Timestamp + 0;
      $q = trim($question->Content);
      $a = trim($question->ChosenAnswer);
      $l = $question->Link;
      
      $s = str_replace("\n", '<br />', htmlentities($s));
      $q = str_replace("\n", '<br />', htmlentities($q));
      $a = str_replace("\n", '<br />', htmlentities($a));

      if (strlen($a)) $qandas[] = array($s, $t, $q, $a, $l);
   }

   return array(count($qandas), $qandas);
}

function SearchYahoo($search, $start, $count) // Recipe 76
{
   // Recipe 76: Search Yahoo!
   //
   // This recipe returns results from the Yahoo1 search
   // engine based on the query in $search, which can be
   // any standard Yahoo! search query. Up to 50 results can
   // be returned at a time. Upon success it returns a two
   // element array, the first of which is the value TRUE,
   // and the second is an array, each element of which
   // contains a sub-array containing the four items in
   // each search result: title, abstract, display url, and
   // click url. On failure it returns a single element
   // array with the value FALSE. It requires the following
   // arguments:
   //
   //    $search: A search query
   //    $start:  The result number from which to start
   //    $count:  The number of results to return

   // IMPORTANT - Replace $id with your own API key obtained at
   //             http://developer.yahoo.com because the search
   //             API is no-longer free and you now need a paid
   //             account.

   $id     = 'Your-own-unique-Yahoo!-search-API-key-goes-here';

   $search = rawurlencode($search);
   $url    = 'http://boss.yahooapis.com/ysearch/web/v1/' .
             "$search?appid=$id&format=xml&start=$start" .
             "&count=$count";

   $xml  = @file_get_contents($url);

   if (!$xml) return array(FALSE);

   $xml  = str_replace('<![CDATA[',        '', $xml);
   $xml  = str_replace(']]>',              '', $xml);
   $xml  = str_replace('&amp;', '[ampersand]', $xml);
   $xml  = str_replace('&',           '&amp;', $xml);
   $xml  = str_replace('[ampersand]', '&amp;', $xml);
   $xml  = str_replace('<b>',     '&lt;b&gt;', $xml);
   $xml  = str_replace('</b>',   '&lt;/b&gt;', $xml);
   $xml  = str_replace('<wbr>', '&lt;wbr&gt;', $xml);
   $sxml = simplexml_load_string($xml);
   $data = array();

   foreach($sxml->resultset_web->result as $result)
   {
      $t = html_entity_decode($result->title);
      $a = html_entity_decode($result->abstract);
      $d = html_entity_decode($result->dispurl);
      $c = $result->clickurl;

      if (strlen($a)) $data[] = array($t, $a, $d, $c);
   }

   return array(count($data), $data);
}

function GetYahooStockNews($stock) // Recipe 77
{
   // Recipe 77:  Get Yahoo! Stock News
   //
   // This recipe takes a stock ticker symbol and then returns
   // any recent news stories on it from finance.yahoo.com.
   // Upon success it returns a three element array, the first
   // of which is the number of new stories returned, the second
   // is a two element array with a URL to a small (192x96) and
   // a large (512x288) intraday price chart for the stock,  and
   // the third element is an array in which each element
   // contains a sub-array of the following five items for each
   // stock: 1) The title of the news story, 2) The site it came
   // from, 3) The date of the story, 4) The story summary, and
   // 5) The URL of the original story. On failure a single
   // element array with the value FALSE us returned. It requires
   // this argument:
   //
   //    $stock: A stock symbol

   date_default_timezone_set('utc');
   $stock = strtoupper($stock);
   $url   = 'http://finance.yahoo.com';
   $check = @file_get_contents("$url/q?s=$stock");

   if (stristr($check, 'Invalid Ticker Symbol') || $check == '')
      return array(FALSE);

   $reports = array();
   $xml     = file_get_contents("$url/rss/headline?s=$stock");
   $xml     = preg_replace('/&lt;\/?summary&gt;/', '', $xml);
   $xml     = preg_replace('/&lt;\/?image&gt;/',   '', $xml);
   $xml     = preg_replace('/&lt;\/?guid&gt;/',    '', $xml);
   $xml     = preg_replace('/&lt;\/?p?link&gt;/',  '', $xml);
   $xml     = str_replace('&lt;![CDATA[',          '', $xml);
   $xml     = str_replace(']]&gt;',                '', $xml);
   $xml     = str_replace('&amp;',      '[ampersand]', $xml);
   $xml     = str_replace('&',                '&amp;', $xml);
   $xml     = str_replace('[ampersand]',      '&amp;', $xml);
   $xml     = str_replace('<b>',          '&lt;b&gt;', $xml);
   $xml     = str_replace('</b>',        '&lt;/b&gt;', $xml);
   $xml     = str_replace('<wbr>',      '&lt;wbr&gt;', $xml);
   $sxml    = simplexml_load_string($xml);

   foreach($sxml->channel->item as $item)
   {
      $flag  = FALSE;
      $url   = $item->link;
      $title = $item->title;
      $temp  = explode(' (', $title);
      $title = $temp[0];
      $site  = str_replace(')',   '', $temp[1]);
      $site  = str_replace('at ', '', $site);
      $desc  = $item->description;
      $date  = date('M jS, g:ia',
         strtotime(substr($item->pubDate, 0, 25)));

      for ($j = 0 ; $j < count($reports) ; ++$j)
      {
         similar_text(strtolower($reports[$j][0]),
            strtolower($title), $percent);

         if ($percent > 70)
         {
            $flag = TRUE;
            break;
         }
      }

      if (!$flag && !strstr($title, '[$$]') && strlen($desc))
         $reports[] = array($title, $site, $date, $desc, $url);
   }

   $url1 = "http://ichart.finance.yahoo.com/t?s=$stock";
   $url2 = "http://ichart.finance.yahoo.com/b?s=$stock";
   return array(count($reports), array($url1, $url2), $reports);
}

function GetYahooNews($search) // Recipe 78
{
   // Recipe 78: Get Yahoo! News
   //
   // This recipe takes a search query and returns matching
   // news items from news.yahoo.com. Upon success it returns
   // a two elemet array, the first being the number of stories
   // returned and the second is an array whoise elements are
   // each sub-arrays containing these details: 1) News story
   // title, 2) Originating site name, 3) Publication date, 4)
   // Story summary, and 5) Full story URL. on failure it will
   // return a single element array with the value FALSE. It
   // requires this argument:
   //
   //    $search: A search query

   date_default_timezone_set('utc');
   $reports = array();
   $url     = 'http://news.search.yahoo.com/news/rss?' .
              'ei=UTF-8&fl=0&x=wrt&p=' . rawurlencode($search);
   $xml     = @file_get_contents($url);
   if (!strlen($xml)) return array(FALSE);

   $xml  = str_replace('<![CDATA[',        '', $xml);
   $xml  = str_replace(']]>',              '', $xml);
   $xml  = str_replace('&amp;', '[ampersand]', $xml);
   $xml  = str_replace('&',           '&amp;', $xml);
   $xml  = str_replace('[ampersand]', '&amp;', $xml);
   $xml  = str_replace('<b>',     '&lt;b&gt;', $xml);
   $xml  = str_replace('</b>',   '&lt;/b&gt;', $xml);
   $xml  = str_replace('<wbr>', '&lt;wbr&gt;', $xml);
   $sxml = simplexml_load_string($xml);

   foreach($sxml->channel->item as $item)
   {
      $flag  = FALSE;
      $url   = $item->link;
      $date  = date('M jS, g:ia', strtotime($item->pubDate));
      $title = $item->title;
      $temp  = explode(' (', $title);
      $title = $temp[0];
      $site  = str_replace(')', '', $temp[1]);
      $desc  = $item->description;

      for ($j = 0 ; $j < count($reports) ; ++$j)
      {
         similar_text(strtolower($reports[$j][0]),
            strtolower($title), $percent);

         if ($percent > 70)
         {
            $flag = TRUE;
            break;
         }
      }

      if (!$flag && strlen($desc))
         $reports[] = array($title, $site, $date, $desc, $url);
   }

   return array(count($reports), $reports);
}

function SearchGoogleBooks($search, $start, $count, $type) // Recipe 79
{
   // Recipe 79: Search Google Books
   //
   // This recipe takes a search query and returns matching
   // books from books.google.com. Upon success it returns
   // a two elemet array, the first being the number of books
   // returned and the second is an array whose elements are
   // each sub-arrays containing these details: 1) Title, 2)
   // Author, 3) Publisher, 4)Date, 5) Description, 6) Info
   // URL, 7) Preview URL. on failure it returns a single
   // element array with the value FALSE. It requires these
   // arguments:
   //
   //    $search: A search query
   //    $start:  The first result to return
   //    $count:  The maximum number of results to return
   //    $type:   If 'none' return all books, if 'partial'
   //             return books with partial previews, if
   //             'full' only return books where the entire
   //             book can be read

   $results = array();
   $url     = 'http://books.google.com/books/feeds/volumes?' .
              'q=' . rawurlencode($search) . '&start-index=' .
              "$start&max-results=$count&min-viewability=" .
              "$type";
   $xml     = @file_get_contents($url);
   if (!strlen($xml)) return array(FALSE);

   $xml  = str_replace('dc:', 'dc', $xml);
   $sxml = simplexml_load_string($xml);

   foreach($sxml->entry as $item)
   {
      $title   = $item->title;
      $author  = $item->dccreator;
      $pub     = $item->dcpublisher;
      $date    = $item->dcdate;
      $desc    = $item->dcdescription;
      $thumb   = $item->link[0]['href'];
      $info    = $item->link[1]['href'];
      $preview = $item->link[2]['href'];

      if (!strlen($pub))
         $pub = $author;
      if ($preview ==
         'http://www.google.com/books/feeds/users/me/volumes')
         $preview = FALSE;
      if (!strlen($desc))
         $desc = '(No description)';
      if (!strstr($thumb, '&sig='))
         $thumb = 'http://books.google.com/googlebooks/' .
            'images/no_cover_thumb.gif';

      $results[] = array($title, $author, $pub, $date, $desc,
         $thumb, $info, $preview);
   }

   return array(count($results), $results);
}

function ConvertCurrency($amount, $from, $to) // Recipe 80
{
   // Recipe 80: Convert Currency
   //
   // This recipe takes a value in one currency and converts
   // it to a value in a second currency. Upon success it
   // returns the converted value, otherwise it returns FALSE.
   // Tip: Once the $main[] array has been populated, you may
   // wish to cache the contents until the next day. This recipe
   // requires the following arguments:
   //
   //    $amount: The amount of the first currency
   //    $from:   The first currency label
   //    $to:     The second currency label
   //
   //    The labels must be one of the following:
   //    AUD, BGN, BRL, CAD, CHF, CNY, CZK, DKK, EEK, EUR, GBP,
   //    HKD, HRK, HUF, IDR, INR, JPY, KRW, LTL, LVL, MXN, MYR,
   //    NOK, NZD, PHP, PLN, RON, RUB, SEK, SGD, THB, TRY, USD,
   //    ZAR

   $url   = 'http://www.ecb.europa.eu/stats/eurofxref/' .
            'eurofxref-daily.xml';
   $data  = file_get_contents($url);
   if (!strlen($data)) return FALSE;

   $ptr1  = strpos($data, '<Cube currency');
   $ptr2  = strpos($data, '</Cube>');
   $data  = substr($data, $ptr1, $ptr2 - $ptr1);
   $data  = str_replace("<Cube currency='", '', $data);
   $data  = str_replace("' rate='",        '|', $data);
   $data  = str_replace("'/>",             '@', $data);
   $data  = preg_replace("/\s/",            '', $data);
   $main  = array();
   $lines = explode('@', substr($data, 0, -1));

   foreach($lines as $line)
   {
      list($l, $r) = explode('|', $line);
      $main[$l]    = $r;
   }

   $main['EUR'] = 1;
   $from        = strtoupper($from);
   $to          = strtoupper($to);
   
   if (!isset($main[$from]) || !isset($main[$to])) return FALSE;
   return sprintf('%.02f', $amount / $main[$from] * $main[$to]);
}

// See JavaScript recipes 85 - 87 in WDC.js for CreateAjaxObject(),
// GetAjaxRequest() & PostAjaxRequest() (PHP recipes 81 - 83)

function ProtectEmail($email) // Recipe 84
{
   // Recipe 84: Protect Email
   //
   // This recipe takes an email address and turns it into a
   // clickable <a href='mailto:...' link using JavaScript
   // to obfusacte the link when viewed using View | Source, or
   // if the page is loaded by a spam email harvesting bot. On
   // success it returns a string containing JavaScript code, or
   // on failure it returns FALSE. It requires this argument:
   //
   //    $email: An email address to protect

   $t1 = strpos($email, '@');
   $t2 = strpos($email, '.', $t1);
   if (!$t1 || !$t2) return FALSE;

   $e1 = substr($email, 0, $t1);
   $e2 = substr($email, $t1, $t2 - $t1);
   $e3 = substr($email, $t2);

   return "<script>e1='$e1';e2='$e2';e3='$e3';document.write" .
          "('<a href=\'mailto:' + e1 + e2 + e3 + '\'>' + e1 " .
          "+ e2 + e3 + '</a>');</script>";
}

function ToggleText($text1, $link1, $text2, $link2) // Recipe 85
{
   // Recipe 85: Toggle Text
   //
   // This recipe takes two pairs of details each comprising
   // a text and a link. When clicked the link text for each
   // causes the other text and link to be displayed, thus
   // toggling between the two. It returns the JavaScript
   // necessary to insert in your web page to achieve this
   // effect. It requires these arguments:
   //
   //    $text1: The original text to display
   //    $link1: The original link text to display
   //    $text2: The alternative text
   //    $link2: the alternative link text

   $tok  = rand(0, 1000000);
   $out  = "<div id='TT1_$tok' style='display:block;'>" .
           "<a href=\"javascript://\" onClick=\"document." .
           "getElementById('TT1_$tok').style.display=" .
           "'none'; document.getElementById('TT2_$tok')" .
           ".style.display='block';\">$link1</a>$text1</div>\n";

   $out .= "<div id='TT2_$tok' style='display:none;'>" .
           "<a href=\"javascript://\" onClick=\"document." .
           "getElementById('TT1_$tok').style.display=" .
           "'block'; document.getElementById('TT2_$tok')" .
           ".style.display='none';\">$link2</a>$text2</div>\n";
   return  $out;
}

function StatusMessage($text, $id, $status) // Recipe 86
{
   // Recipe 86: Status Message
   //
   // This recipe takes some text that will activate a status
   // message when rolled over with the mouse, the ID of an
   // HTML element whose contents should be used for a status
   // message, and the status message to use. It requires these
   // arguments:
   //
   //    $text:   The text to display and activate a status
   //    $id:     The ID of an HTML element
   //    $status: The message to insert in $id

   $target = "getElementById('$id').innerHTML";
   return    "<span onMouseOver=\"temp=$target; " .
             "$target='$status';\" onMouseOut=\"$target=" .
             "temp;\">$text</span>";
}

function SlideShow($images) // Recipe 87
{
   // Recipe 87: Slide Show
   //
   // This recipe takes an array of URLs containing images
   // and returns the JavaScript required to display them in a
   // slideshow. It requires this argument:
   //
   //    $images: An array of image URLs

   $count = count($images);
   $out   = "<script>images = new Array($count);\n";

   for ($j=0 ; $j < $count ; ++$j)
   {
      $out .= "images[$j] = new Image();";
      $out .= "images[$j].src = '$images[$j]'\n";
   }

   $out .= <<<_END
counter = 0
step    = 4
fade    = 100
delay   = 0
pause   = 250
startup = pause

load('SS1', images[0]);
load('SS2', images[0]);
setInterval('process()', 20);

function process()
{
   if (startup-- > 0) return;

   if (fade == 100)
   {
      if (delay < pause)
      {
         if (delay == 0)
         {
            fade = 0;
            load('SS1', images[counter]);
            opacity('SS1', 100);
            ++counter;

            if (counter == $count) counter = 0;

            load('SS2', images[counter]);
            opacity('SS2', 0);
         }
         ++delay;
      }
      else delay = 0;
   }
   else
   {
      fade += step;
      opacity('SS1', 100 - fade);
      opacity('SS2', fade);
   }
}

function opacity(id, deg)
{
    var object          = $(id).style;
    object.opacity      = (deg/100);
    object.MozOpacity   = (deg/100);
    object.KhtmlOpacity = (deg/100);
    object.filter       = "alpha(opacity = " + deg + ")";
}

function load(id, img)
{
   $(id).src = img.src;
}

function $(id)
{
   return document.getElementById(id)
}

</script>
_END;

   return $out;
}

function InputPrompt($params, $prompt) // Recipe 88
{
   // Recipe 88: Input Prompt
   //
   // This recipe returns the HTML and JavaScript required
   // to add a prompt to an input field which is only displayed
   // when that field has an empty value. It requires these
   // arguments:
   //
   //    $params: Parameters to control the input such as
   //             name=, type=, rows=, cols=, name=, size=
   //             value=, and so on
   //    $prompt: The prompt text to display

   $id = 'IP_' . rand(0, 1000000);

   $out = <<<_END
<input id='$id' $params
   onFocus="IP1('$id', '$prompt')"
   onBlur="IP2('$id', '$prompt')" />
_END;

   static $IP_NUM;
   if ($IP_NUM++ == 0) $out .= <<<_END
<script>
IP2('$id', '$prompt')

function IP1(id, prompt)
{
   if ($(id).value == prompt) $(id).value = ""
}

function IP2(id, prompt)
{
   if ($(id).value == "") $(id).value = prompt
}

function $(id)
{
   return document.getElementById(id)
}
</script>
_END;
   return $out;
}

function WordsFromRoot($word, $filename, $max) // Recipe 89
{
   // Recipe 89: Words From Root
   //
   // This recipe takes a word or word part and then returns
   // an array of all the words in the word file that begin
   // with the supplied word. It requires these arguments:
   //
   //    $word:       A word to look up
   //    $dictionary: The location of a list of words separated
   //                 by non-word or space characters such as
   //                 \n or \r\n

   $dict = file_get_contents($filename);
   preg_match_all('/' . $word . '[\w ]+/', $dict, $matches);
   $c    = min(count($matches[0]), $max);
   $out  = array();

   for ($j = 0 ; $j < $c ; ++$j) $out[$j] = $matches[0][$j];
   return $out;
}

function PredictWord($params, $view, $max) // Recipe 90
{
   // Recipe 90: Predict Word
   //
   // This recipe returns the HTML and JavaScript required
   // to provide a selection of predicted words or phrases
   // which a user can opt to click on to complete a form
   // input. It requires these arguments:
   //
   //    $params: Parameters to control the input such as
   //             name=, type=, rows=, cols=, name=, size=
   //             value=, and so on
   //    view:    The maximum number of items to display at
   //             a time in the selection list (if there are
   //             more the list becomes scrollable)
   //    $max:    The maximum number of words to offer

   $id = rand(0, 1000000);

   $out = "<input id='PWI_$id' $params " .
          "onKeyUp='PredictWord($view, $max, $id)'>" .
          "<br /><select id='PWS_$id' " .
          "style='display:none' />\n";

   for ($j = 0 ; $j < $max ; ++$j)
      $out .= "<option id='PWO_$j" . "_$id' " .
              "onClick='CopyWord(this.id, $id)'>";

   $out .= '</select>';
   static $PW_NUM;
   if ($PW_NUM++ == 0) $out .= <<<_END
<script>
function CopyWord(id1, id2)
{
   $('PWI_' + id2).value = $(id1).innerHTML
   $('PWS_' + id2).style.display = 'none';
}

function PredictWord(view, max, id)
{
   if ($('PWI_' + id).value.length > 0)
   {
      GetAjaxRequest2('wordsfromroot.php',
         'word=' + $('PWI_' + id).value +
         '&max=' + max, view, max, id)
      $('PWS_' + id).scrollTop = 0
      $('PWO_0_' + id).selected = true
   }
   else $('PWS_' + id).style.display = 'none'
}

function GetAjaxRequest2(url, params, view, max, id)
{
   nocache = "&nocache=" + Math.random() * 1000000
   request = new AjaxRequest()
   
	request.onreadystatechange = function()
   {
      if (this.readyState == 4)
         if (this.status == 200)
            if (this.responseText != null)
            {
               a = this.responseText.split('|')
               c = 0

               for (j in a)
               {
                  $('PWO_' + c + '_' + id).
                     innerHTML = a[j]
                  $('PWO_' + c++ + '_' + id).
                     style.display = 'block'
               }

               n = c > view ? view : c
               while (c < max)
               {
                  $('PWO_' + c++ + '_' + id).
                     style.display = 'none'
               }
               $('PWS_' + id).size = n;
               $('PWS_' + id).style.display = 'block'
            }

   // You can remove these two alerts after debugging
            else alert("Ajax error: No data received")
         else alert( "Ajax error: " + this.statusText)
   }

   request.open("GET", url + "?" + params + nocache, true)
   request.send(null)
}

function AjaxRequest()
{
   try
   {
      var request = new XMLHttpRequest()
   }
   catch(e1)
   {
      try
      {
         request = new ActiveXObject("Msxml2.XMLHTTP")
      }
      catch(e2)
      {
         try
         {
            request = new ActiveXObject("Microsoft.XMLHTTP")
         }
         catch(e3)
         {
            request = false
         }
      }
   }
   return request
}

function $(id)
{
   return document.getElementById(id)
}

</script>
_END;
   return $out;
}

function GetCountryFromIP($ip) // Recipe 91
{
   // Recipe 91: Get Country From IP
   //
   // This recipe returns the country associated with a
   // supplied IP number. It requires this argument:
   //
   //    $ip: An IP address

   $iptemp = explode('.', $ip);
   $ipdec  = $iptemp[0] * 256 * 256 * 256 +
             $iptemp[1] * 256 * 256 +
             $iptemp[2] * 256 +
             $iptemp[3];
   $file  = file_get_contents('ips.txt');
   if (!strlen($file)) return FALSE;

   $lines = explode("\n", $file);

   foreach($lines as $line)
   {
      if (strlen($line))
      {
         $parts = explode(',', trim($line));

         if ($ipdec >= $parts[0] && $ipdec <= $parts[1])
            return $parts[2];
      }
   }

   return FALSE;
}

function BypassCaptcha() // Recipe 92
{
   // Recipe 92: Captcha Bypass
   //
   // This recipe checks whether it looks like a real person
   // is using your website and returns TRUE if so, otherwise
   // it returns FALSE. It requires no arguments

   if (isset($_SERVER['HTTP_REFERER']) &&
       isset($_SERVER['HTTP_USER_AGENT']))
         return TRUE;
   return FALSE;
}

function GetBookFromISBN($isbn) // Recipe 93
{
   // Recipe 93: Get Book From ISBN
   //
   // This recipe looks up an ISBN-10 at Amazon.com and then
   // returns the matching book title and a thumbnail image
   // of the front cover. It requires this argument:
   //
   //    $isbn: The ISBN to look up
   //
   // Updated from the function in the book to take into
   // account changes to the Amazon HTML.

   // The following line is in the book but doesn't work because Amazon made a change

	//   $find = '<div class="dpProductTitle">';

	// Instead the following line replaces it:

   $find = '<title>';

   $url  = "http://www.amazon.com/gp/aw/d/$isbn";
   $img  = 'http://ecx.images-amazon.com/images/I';
   $nf   = '<i>Title not found on the Amazon US website</i>';
   $none = 'http://g-ecx.images-amazon.com/images/G/01/x-site/' .
           'icons/no-img-sm._AA75_.gif';

   $page = CurlGetContents($url, "");
   if (!strlen($page)) return array($nf, $none);

   $ptr1 = strpos($page, $find);
   if (!$ptr1) return array($nf, $none);

   $ptr1 += strlen($find);

   // The following line is in the book but doesn't work because Amazon made a change

   //   $ptr2  = strpos($page, '"</div>', $ptr1);

	// Instead the following line replaces it:

   $ptr2  = strpos($page, '"</title>', $ptr1);

   $title = substr($page, $ptr1, $ptr2 - $ptr1);
   $find  = $img;
   $ptr1  = strpos($page, $find) + strlen($find);
   $ptr2  = strpos($page, '"', $ptr1);
   $image = substr($page, $ptr1, $ptr2 - $ptr1);

   return array($title, $img . $image);
}

function GetAmazonSalesRank($isbn, $site) // Recipe 94
{
   // Recipe 94: Get Amazon Sales Rank
   //
   // This recipe looks up an ISBN-10 at the chosen Amazon
   // website and returns the book's Sales Rank at that site.
   // It requires these arguments:
   //
   //    $isbn: The ISBN to look up
   //    $site: The Amazon website to use, out of:
   //           amazon.com, amazon.ca, amazon.co.uk, amazon.fr,
   //           amazon.de, amazon.it, amazon.es, amazon.cn and
   //           amazon.co.jp
   
   $url = "http://www.$site/gp/aw/d/$isbn?pd=1";
 
   // The following line has been replaced with the one following it due to an Amazon change
   // $end = '</div>';

   $end = '<br />';

   switch(strtolower($site))
   {
      case 'amazon.com':
      case 'amazon.ca':
      case 'amazon.co.uk':
         $find = 'Sales Rank: ';
         break;
      case 'amazon.cn':
         $find = 'Amazon.cné”€å”®æŽ’è¡Œæ¦œ: ';
         break;
      case 'amazon.de':
         $find = 'Verkaufsrang: ';
         break;
      case 'amazon.es':
         $find = 'ventas de Amazon.es: ';
         break;
      case 'amazon.fr':
         $find = 'ventes Amazon.fr: ';
         break;
      case 'amazon.it':
         $find = "vendite Amazon.it: ";
         break;
      case 'amazon.co.jp':
         $find = '<li id="SalesRank">';
         $url  = "http://amazon.co.jp/gp/product/$isbn";
         $end  = '(<a';
         break;
   }

   $page = CurlGetContents($url, "");
   if (!strlen($page)) return FALSE;

   $ptr1 = strpos($page, $find);
   if (!$ptr1) return FALSE;

   $ptr2 = strpos($page, $end, $ptr1);
   $temp = substr($page, $ptr1, $ptr2 - $ptr1);
   return trim(preg_replace('/[^\d]/', '', $temp));
}

function PatternMatchWord($word, $dictionary) // Recipe 95
{
   // Recipe 95: Pattern Match Word
   //
   // This recipe searches a dictionary of words for all those
   // matching a given pattern. Upon success it retuns a two
   // element array, the first of which is is the number of
   // matches and the second is an array containing the
   // matches. On failure it returns a single element array
   // with the value FALSE. It requires these arguments:
   //
   //    $word:       A word to look up
   //    $dictionary: The location of a list of words separated
   //                 by non-word or space characters such as
   //                 \n or \r\n

   $dict = @file_get_contents($dictionary);
   if (!strlen($dict)) return array(FALSE);
   $word = preg_replace('/[^a-z\.]/', '', strtolower($word));
   preg_match_all('/\b' . $word . '\b/', $dict, $matches);
   return array(count($matches[0]), $matches[0]);
}

function SuggestSpelling($word, $dictionary) // Recipe 96
{
   // Recipe 96: Suggest Spelling
   //
   // This recipe should be supplied with a misspelled word
   // using which it will search $dictionary for words it
   // believes the user may have meant to have typed in. Upon
   // success it returns a two element array, the first of
   // which is the number of possible words it suggests, and
   // the second is an array of words in order of likelihood.
   // On failure a single element array with the value FALSE
   // is returned. It requires these arguments:
   //
   //    $word:       A word to look up
   //    $dictionary: The location of a list of words separated
   //                 by non-word or space characters such as
   //                 \n or \r\n

   if (!strlen($word)) return array(FALSE);

   static $count, $words;

   if ($count++ == 0)
   {
      $dict = @file_get_contents($dictionary);
      if (!strlen($dict)) return array(FALSE);
      $words = explode("\r\n", $dict);
   }

   $possibles = array();
   $known     = array();
   $suggested = array();
   $wordlen   = strlen($word);
   $chars     = str_split('abcdefghijklmnopqrstuvwxyz');

   for($j = 0 ; $j < $wordlen ; ++$j)
   {
      $possibles[] =    substr($word, 0, $j) .
                        substr($word, $j + 1);

      foreach($chars as $letter)
         $possibles[] = substr($word, 0, $j) .
                        $letter .
                        substr($word, $j + 1);
   }

   for($j = 0; $j < $wordlen - 1 ; ++$j)
      $possibles[] =    substr($word, 0, $j) .
                        $word[$j + 1] .
                        $word[$j] .
                        substr($word, $j +2 );

   for($j = 0; $j < $wordlen + 1 ; ++$j)
      foreach($chars as $letter)
         $possibles[] = substr($word, 0, $j).
                        $letter.
                        substr($word, $j);

   $known = array_intersect($possibles, $words);
   $known = array_count_values($known);
   arsort($known, SORT_NUMERIC);

   foreach ($known as $temp => $val)
      $suggested[] = $temp;

   return array(count($suggested), $suggested);
}

function AnagramFinder($word, $filename) // Recipe 97
{
   // Recipe 97: Anagram Finder
   //
   // This recipe takes a word or and then returns an array
   // of all the words in the word file that are full anagrams
   // of the word. It requires these arguments:
   //
   //    $word:       A word to look up
   //    $dictionary: The location of a list of words separated
   //                 by non-word or space characters such as
   //                 \n or \r\n

   $dict  = file_get_contents($filename);
   $check = '';
   $w     = strlen($word);
   $out   = array();

   for ($j = 0 ; $j < $w ; ++$j)
      $check .= "[$word]";

   preg_match_all('/[\n\r](' . $check . ')[\n\r]/', $dict, $matches);

   for ($j = 0 ; $j < count($matches[0]); ++$j)
   {
      $maybe = trim($matches[0][$j]);
      $t     = " $maybe";
      $found = TRUE;

      for ($k = 0 ; $k < $w ; ++$k)
	  {
	     $p = strpos($t, $word[$k]);

	     if ($p) $t[$p] = ' ';
		 else    $found = FALSE;
	  }

      if ($found && $word != $maybe)
	     array_push($out, $maybe);
   }

   return $out;
}

function CornerGif($corner, $border, $bground) // Recipe 98
{
   // Recipe 98: Corner Gif
   //
   // This recipe creates a gif image suitable for building
   // rounded table edges. On success it returns a GD image.
   // On failure it returns FALSE. It requires these
   // arguments:
   //
   //    $corner:  The corner type (which includes edges) out
   //              of tl, t, tr, l, r, bl, b and br for top-
   //              left, top, top-right, left, right, bottom-
   //              left, bottom and bottom-right
   //    $border:  The border color as six hexadecimal digits
   //    $bground: The fill color as six hexadecimal digits

   $data  = array(array(0, 0, 0, 0, 0),
                  array(0, 0, 0, 1, 1),
                  array(0, 0, 1, 2, 2),
                  array(0, 1, 2, 2, 2),
                  array(0, 1, 2, 2, 2));

   $image = imagecreatetruecolor(5, 5);
   $bcol  = GD_FN1($image, $border);
   $fcol  = GD_FN1($image, $bground);
   $tcol  = GD_FN1($image, 'ffffff');

   imagecolortransparent($image, $tcol);
   imagefill($image, 0 , 0, $tcol);

   if (strlen($corner) == 2)
   {
      for ($j = 0 ; $j < 5 ; ++$j)
      {
         for ($k = 0 ; $k < 5 ; ++ $k)
         {
            switch($data[$j][$k])
            {
               case 1: imagesetpixel($image, $j, $k, $bcol); break;
               case 2: imagesetpixel($image, $j, $k, $fcol); break;
            }
         }
      }
   }
   else
   {
      imagefilledrectangle($image, 0, 0, 4, 0, $bcol);
      imagefilledrectangle($image, 0, 1, 4, 4, $fcol);
   }

   switch($corner)
   {
      case 'tr': case 'r':
         $image = imagerotate($image, 270, $tcol); break;
      case 'br': case 'b':
         $image = imagerotate($image, 180, $tcol); break;
      case 'bl': case 'l':
         $image = imagerotate($image,  90, $tcol); break;
   }
   
   return $image;
}

function RoundedTable($width, $height, $bground, $border, $contents, $program) // Recipe 99
{
   // Recipe 99: Rounded Table
   //
   // This recipe takes the contents of $contents and places it
   // inside a table to which it gives rounded borders. It
   // requires these arguments:
   //
   //    $width:    Width of table (optional), to leave at
   //               default set to "" or NULL
   //    $height:   Height of table (optional), to leave at
   //               default set to "" or NULL
   //    $bground:  Background fill color of table
   //    $border:   Border color of table
   //    $contents: Table contents (may include HTML)
   //    $program:  Location of the corner.php program

   if ($width)  $width  = "width='$width'";
   if ($height) $height = "height='$height'";

   $t1 = "<td width='5'><img src='$program?c";
   $t2 = "<td background='$program?c";
   $t3 = "<td width='5' background='$program?c";
   $t4 = "$border&f=$bground' /></td>";
   $t5 = "<td bgcolor='#$bground'>$contents</td>";

   return <<<_END
   <table border='0' cellpadding='0' cellspacing='0'
      $width $height>
   <tr>$t1=tl&b=$t4 $t2=t&b=$t4 $t1=tr&b=$t4</tr>
   <tr>$t3=l&b=$t4 $t5 $t3=r&b=$t4</tr>
   <tr>$t1=bl&b=$t4 $t2=b&b=$t4 $t1=br&b=$t4</tr></table>
_END;
   
}

function DisplayBingMap($lat, $long, $zoom, $style, $width, $height) // Recipe 100
{
   // Recipe 100: Display Bing Map
   //
   // This recipe takes the contents of $contents and places it
   // inside a table to which it gives rounded borders. It
   // requires these arguments:
   //
   //    $lat:    Latitude of the location to display
   //    $long:   Longitude of the location to display
   //             These may be obtained by visiting the URL:
   //                http://www.hmmm.ip3.co.uk/longitude-latitude
   //    $zoom:   The zoom level between 0 (minimum zoom) and
   //             19 (maximum zoom)
   //    $style:  One of Aerial or Road (exact spelling required)
   //    $width:  Width of map
   //    $height: Height of map

   if ($style != 'Aerial' && $style != 'Road') $style = 'Road';

   $width  .= 'px';
   $height .= 'px';

   $root = 'http://ecn.dev.virtualearth.net/mapcontrol';
   return <<<_END
<script src="$root/mapcontrol.ashx?v=6.2"></script>
<script>
if (window.attachEvent)
{
   window.attachEvent('onload',   Page_Load)
   window.attachEvent('onunload', Page_Unload)
}
else
{
   window.addEventListener('DOMContentLoaded', Page_Load, false)
   window.addEventListener('unload', Page_Unload, false)
}

function Page_Load()
{
   GetMap()
}  

function Page_Unload()
{
   if (map != null)
   {
      map.Dispose()
      map = null
   }
}

function GetMap()
{
   map = new VEMap('DBM')
   map.LoadMap(new VELatLong($lat, $long),
      $zoom, VEMapStyle.$style, false)
}
</script>
<div id='DBM' style="position:relative;
   width:$width; height:$height;"></div>
_END;
}

?>