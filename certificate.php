<?php 
    
    $result = $_GET['result'];
    $qCount = $_GET['qcount'];

    $text = "Поздравляем!\nВаш результат:$result из $qCount";

    $image = imagecreatetruecolor(400, 300);
    $backColor = imagecolorallocate($image, 204, 255, 255);
    $textColor = imagecolorallocate($image, 102, 102, 102);

    $fontFile = __DIR__.'/fonts/Lobster-Regular.ttf';

    if (!file_exists($fontFile)) {
        echo 'Файл со шрифтом не найден!';
        exit;
    }

    // $imBox = imagecreatefrompng(__DIR__.'/images/certificate.png');

    imagefill($image, 0, 0, $backColor);
    // imagecopy($image, $imBox, 10, 10, 0, 0, 256, 256);
    imagettftext($image, 20, 0, 0, 100, $textColor, $fontFile, $text);
    header('Content-type: image/png');
    imagepng($image);
    imagedestroy($image);

?>
