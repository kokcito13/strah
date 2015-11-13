
<?php header('Content-Type: text/html; charset=utf-8', true);

setlocale(LC_ALL, 'en_US.UTF8');
?>


<html>
<body>
<?php if (isset($_POST) && !isset($_POST['textOne'])) {  ?>


    <form method="post">
        <textarea name="textOne"></textarea>
        <input type="submit" value="submit"/>
    </form>
<?php  } elseif (isset($_POST['textOne'])){ ?>
    <?php $good = showInfo() ?>

    <form method="post">
        <textarea name="textTwo"><?=$_POST['textOne']?></textarea><br/>
        True: <input type="checkbox" name="trueFalse" <?php if ($good) { ?>checked="checked"<?php }?> /> ? <br/><br/>
        <input type="submit" value="submit"/>
    </form>
<?php } ?>
</body>
</html>


<?php
$dbh = new PDO('mysql:host=localhost;dbname=test;charset=UTF8', 'root', '');
if (isset($_POST['textOne'])) {
    try {
        // foreach($dbh->query('SELECT * from words') as $row) {
        //     echo($row['name']);
        // }
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
} elseif (isset($_POST['textTwo'])) {

    $stmt = $dbh->prepare("INSERT INTO vector_rows (id, status) VALUES (NULL, ". (isset($_POST['trueFalse'])?"1":"0") .")");

    $stmt->execute();
    $rowId = $dbh->lastInsertId();

    $words = clearWords($_POST['textTwo']);

    try {
        foreach ($words as $word) {
            if (empty($word)) continue;

            $wordDB = getWord($dbh, $word);

            $stmt = $dbh->prepare("INSERT INTO words_in (vector_row_id, word_id) VALUES (".$rowId.", ".$wordDB['id'].")");
            $stmt->execute();
        }
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }

    echo "DOne";
}

function clearWords($text)
{
    $text = preg_replace("|\b[\d\w]{1,3}\b|i", "", $text);
    // Удаляем знаки припенания
    $text = preg_replace("~[\.\,\(\)\!\?\+\-\_\=\"]+~", " ", $text);
    // Удаляем лишние пробельные символы
    $text = preg_replace("|[\s]+|i", " ", $text);

    $words = explode(' ', $text);
    foreach ($words as $k => $v) {
        $v = trim($v);
        $v = mb_strtolower($v, 'utf8');
        if (empty($v) || mb_strlen($v, 'utf8') < 4){
            unset($words[$k]);
            continue;
        }

        // $words[$k] = $v;
    }

    return $words;
}

function getWord($dbh, $word)
{
    $word = trim($word);
    $word = mb_strtolower($word, 'UTF-8');

    foreach($dbh->query("SELECT * from words WHERE word LIKE '".$word."' LIMIT 1 ") as $row) {
        return array('id'=> $row['id'] ,'name' => $row['name']);
    }

// var_dump($word);
    $stmt = $dbh->prepare("INSERT INTO words (id, name) VALUES (NULL, :val) ");// ".$word."

    $convertedText = mb_convert_encoding($word, 'utf-8', mb_detect_encoding($word));

    $stmt->bindParam(':val', $convertedText, PDO::PARAM_STR);
    $stmt->execute();

// exit;

    $wordsId = $dbh->lastInsertId();

    return array('id'=> $wordsId ,'name' => $word);
}

function showInfo()
{
    $dbh = new PDO('mysql:host=localhost;dbname=test;charset=UTF8', 'root', '');

    $wordsDB = array();
    $mainX = array();

    $modelsTrue = array();
    $modelsFalse = array();

    foreach($dbh->query('SELECT * from words') as $row) {
        $mainX[md5($row['name'])] = $row['name'];
        $wordsDB[$row['id']] = $row['name'];

        $modelsTrue[md5($row['name'])] = 0;
        $modelsFalse[md5($row['name'])] = 0;
    }

    $newVectorX = array();
    $words = clearWords($_POST['textOne']);
    foreach($words as $word) {
        $word = trim($word);
        $word = mb_strtolower($word, 'utf8');
        $newVectorX[md5($word)] = $word;
    }

    $newArray = array_merge($mainX, $newVectorX);

    $rowsX = array();
    foreach($dbh->query('SELECT words_in . * , vector_rows.status from words_in JOIN vector_rows ON vector_rows.id = words_in.`vector_row_id` ') as $row) {
        if (!isset($rowsX[$row['vector_row_id']]))
            $rowsX[$row['vector_row_id']] = array(
                'status' => $row['status'],
                'data' => array(
                    md5($wordsDB[$row['word_id']]) => 1,
                )
            );
        else {
            $rowsX[$row['vector_row_id']]['data'][md5($wordsDB[$row['word_id']])] = 1;
        }

        if ($row['status']) {
            $modelsTrue[md5($wordsDB[$row['word_id']])]++;
        } else {
            $modelsFalse[md5($wordsDB[$row['word_id']])]++;
        }
    }

    echo '<pre>';

    $countStatuses = array(0=>0, 1=>0);

    $b = count(array_intersect_key($newVectorX, $newArray));
    foreach ($rowsX as $row) {
        $c = count(array_intersect_key($newVectorX, $row['data']));
        $a = count(array_intersect_key($newArray, $row['data']));

        $per = ($c > 0)?(($c)/( ($a>$b)?$a:$b )):0;

        // echo $c." - ".$a ." - ".$b."| - |". $per ."| - ".$row['status'].PHP_EOL;
        $countStatuses[$row['status']]++;
    }

    echo ''.PHP_EOL;
    echo '<table border="1"><tr><td></td>';
    foreach ($newVectorX as $k=>$v) {
        echo '<td>'.$v.'</td>';
    }
    echo '<td></td></tr><tr><td>True model</td>';
    $kNT = 0;
    foreach ($newVectorX as $k=>$v) {
        $num = (isset($modelsTrue[$k])?$modelsTrue[$k]:0);
        $num = ($num?($num/$countStatuses[1]):0);
        $kNT = $kNT+$num;
        echo '<td>'.$num.'</td>';
    }

    echo '<td>'.$kNT.'</td></tr><tr><td>False model</td>';
    $kNf = 0;
    foreach ($newVectorX as $k=>$v) {
        $num = (isset($modelsFalse[$k])?$modelsFalse[$k]:0);
        $num = ($num?($num/$countStatuses[0]):0);
        $kNf = $kNf+$num;
        echo '<td>'.$num.'</td>';
    }

    echo '<td>'.$kNf.'</td></tr></table>';

    echo "Хороший коммент на: ".$kNT.PHP_EOL;
    echo "Плохой коммент на: ".$kNf.PHP_EOL;

// var_dump($modelsTrue);
// var_dump($modelsFalse);

    // var_dump($mainX);
    // var_dump($newVectorX);
    // var_dump($newArray);
    // var_dump($rowsX);

    // exit;

    return $kNT>$kNf;
}