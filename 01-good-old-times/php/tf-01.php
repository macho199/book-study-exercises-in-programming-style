<?php

function touchopen($filename, ...$params) {
    if (file_exists($filename)) {
        unlink($filename);
    }
    fclose(fopen($filename, 'a'));
    return fopen($filename, ...$params);
}

$data = [];

$f = fopen('../../stop_words.txt', 'r');
$data = [fread($f, 1024)];
fclose($f);

$data[] = [];       // $data[1]은 (최대 80자인) 줄
$data[] = null;     // $data[2]는 단어의 시작 문자 색인
$data[] = 0;        // $data[3]은 문자에 대한 색인이며 i = 0
$data[] = false;    // $data[4]는 단어를 찾았는지 여부를 나타내는 플래그
$data[] = '';       // $data[5]는 해당 단어
$data[] = '';       // $data[6]은 단어, NNNN
$data[] = 0;        // $data[7]은 빈도

// 보조 기억 장치를 연다
$word_freqs = touchopen('word_freqs', 'rb+');
$f = fopen($argv[1], 'r');

$i = 0;
while (true) {
    $data[1] = [fgets($f)];
    if ($data[1] == ['']) {
        break;
    }
    if ($data[1][0][strlen($data[1][0])-1] != PHP_EOL) {
        $data[1][0] = $data[1][0] . PHP_EOL;
    }
    $data[2] = null;
    $data[3] = 0;

    foreach (str_split($data[1][0]) as $c) {
        if (preg_match('/[\dA-Za-z]/', $c)) {
            $data[2] = $data[3];
        } else {
            if (!preg_match('/[\dA-Za-z]/', $c)) {
                
            }
        }
    }
    var_dump($data);

    ++$i;
    if ($i == 20) {
        break;
    }
}
