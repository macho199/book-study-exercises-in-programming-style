<?php

function touchopen($filename, ...$params) {
    if (file_exists($filename)) {
        unlink($filename);
    }
    fclose(fopen($filename, 'a'));
    return fopen($filename, ...$params);
}

$data = [];

$f = fopen('stop_words.txt', 'r');
$data = [fread($f, 1024)];
fclose($f);

$data[] = [];       // $data[1]은 (최대 80자인) 줄
$data[] = null;     // $data[2]는 단어의 시작 문자 색인
$data[] = 0;        // $data[3]은 문자에 대한 색인이며 i = 0
$data[] = false;    // $data[4]는 보조기억장치에서 단어를 찾았는지 여부를 나타내는 플래그
$data[] = '';       // $data[5]는 해당 단어
$data[] = '';       // $data[6]은 단어, NNNN
$data[] = 0;        // $data[7]은 빈도

// 보조 기억 장치를 연다
$word_freqs = touchopen('word_freqs', 'rb+');
$f = fopen($argv[1], 'r');
while (!feof($f)) {
    $data[1] = [fgets($f)];
    // if ($data[1][0][strlen($data[1][0])-1] != PHP_EOL) {
    //     $data[1][0] = $data[1][0] . PHP_EOL;
    // }
    $data[2] = null;
    $data[3] = 0;

    foreach (str_split($data[1][0]) as $c) {
        // $data[2]가 null 이고, 현재 $c가 문자나 숫자라면
        if (is_null($data[2])) {
            if (preg_match('/[\dA-Za-z]/', $c)) {
                $data[2] = $data[3];
            }
        } else {
            if (!preg_match('/[\dA-Za-z]/', $c)) {
                $data[4] = false;
                $data[5] = strtolower(substr($data[1][0], $data[2], $data[3] - $data[2]));

                if (strlen($data[5]) >= 2 && !strpos($data[0], $data[5])) {
                    while (!feof($word_freqs)) {
                        $data[6] = trim(fgets($word_freqs));
                        if ($data[6] == '') {
                            break;
                        }
                        
                        $data[7] = (int)explode(',', $data[6])[1];
                        $data[6] = trim(explode(',', $data[6])[0]);

                        if ($data[5] == $data[6]) {
                            ++$data[7];
                            $data[4] = true;
                            break;
                        }
                    }

                    if (!$data[4]) {
                        fseek($word_freqs, 0, SEEK_CUR);
                        fwrite($word_freqs, sprintf('%20s,%04d%s', $data[5], 1, PHP_EOL));
                    } else {
                        // windows의 경우 -27
                        fseek($word_freqs, in_array(PHP_OS, ['WIN32', 'WINNT', 'Windows']) ? -27 : -26, SEEK_CUR);
                        fwrite($word_freqs, sprintf('%20s,%04d%s', $data[5], $data[7], PHP_EOL));
                    }
                    fseek($word_freqs, 0, SEEK_SET);
                }
                $data[2] = null;
            }
        }
        ++$data[3];
    }
}

fclose($f);
fflush($word_freqs);

$data = [];

foreach (range(1, 25) as $i) {
    $data[] = [];
}
$data[] = ''; // $data[25] 단어
$data[] = 0;  // $data[26] 빈도수

while (!feof($word_freqs)) {
    $data[25] = trim(fgets($word_freqs));
    if ($data[25] == '') {
        break;
    }

    $data[26] = (int)explode(',', $data[25])[1];
    $data[25] = trim(explode(',', $data[25])[0]);

    foreach (range(0, 24) as $i) {
        if ($data[$i] == [] || $data[$i][1] < $data[26]) {
            if ($i == 0) {
                array_unshift($data, [$data[25], $data[26]]);
            } else {
                $data = array_merge(array_slice($data, 0, $i), [[$data[25], $data[26]]], array_slice($data, $i));
            }
            array_splice($data, count($data)-1);
            break;
        }
    }
}

foreach (range(0, 24) as $i) {
    if (!empty($data[$i])) {
        print($data[$i][0] . ' - ' . $data[$i][1] . PHP_EOL);
    }
}
