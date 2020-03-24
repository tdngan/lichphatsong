<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php


$word = new COM("word.application") or die ("Could not initialise MS Word object.");
$word->Documents->Open(realpath("TH.CN.doc"));

// Extract content.
$content = (string) $word->ActiveDocument->Content;

echo $content;
//echo mb_detect_encoding ($content);
//echo utf8_encode($content);
echo utf8_decode($content);
//echo mb_convert_encoding($content, 'UTF-8', 'UTF-8');

$word->ActiveDocument->Close(false);

$word->Quit();
$word = null;
unset($word);
?>