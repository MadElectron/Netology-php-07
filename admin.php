<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Загрузка файла</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Загрузка теста</h1>
        <nav>
            <ul>
                <li>Загрузка теста</li>
                <li><a href="list.php" title="Список тестов">Список тестов</a></li>
            </ul>
        </nav>
        <hr>
        
        <form action="admin.php" enctype="multipart/form-data" method="post">
            <label>Выберите файл</label>
            <input type="file" name="file"><br>
            <input type="submit" name="submit" value="Загрузить">
        </form>
        <?php 
            
            function checkTestsErrors($filename) {

                // $json = file_get_contents('tests/'.$filename);
                $json = file_get_contents($filename);
                $data = json_decode($json, true);
                $errCount = 0;

                if (!count($data)) {
                    echo '<p class="alert">Список вопросов пуст!</p>';
                }

                // Checking test consistncy
                $testParams = ['id' => 'Номер', 'name' => 'Название', 'questions' => 'Список вопросов'];

                foreach ($data as $testNum => $test) {
                    if ($testNum) {
                        echo '<hr>';
                    }

                    $missingParams = array_diff(array_keys($testParams), array_keys($test));
           
                    if ($missingParams) {
                        $errCount++;
                        echo '<p class="alert">'.$testNum.'-й тест не имеет следующих параметров:</p><ul>';

                        foreach($missingParams as $param) {
                            echo '<li>'.$testParams[$param].'</li>';
                        }

                        echo '</ul>';
                        continue;
                    }

                    // Test info output
                    echo '<h3><strong>Тест '.$test['id'].'. '.$test['name'].'</strong></h3>';

                    if (!count($test['questions'])) {
                        $errCount++;
                        echo '<p class="alert">Список вопросов пуст.</p>';
                        continue;
                    }

                    // Checking question consistncy
                    $questionParams = ['id' => 'Номер', 'content' => 'Содержание вопроса', 'answers' => 'Ответы'];

                    foreach($test['questions'] as $qNum => $question) {
                        $missingParams = array_diff(array_keys($questionParams), array_keys($question));

                        if ($missingParams) {
                            $errCount++;
                            echo '<p class="alert">'.$qNum.'-й вопрос не имеет следующих параметров</p><ul>';

                            foreach($missingParams as $param) {
                                echo "<li>$questionParams[$param]</li>";
                            }

                            echo '</ul>';
                        }

                        // Question info output
                        echo '<p><strong>Вопрос '.$question['id'].': '.$question['content'].'</strong></p>';

                        if (!count($question['answers'])) {
                            $errCount++;
                            echo '<p class="alert">Список ответов пуст.</p>';
                            continue;
                        }

                        // Checking answer consistncy
                        $answerParams = ['content' => 'Содержание ответа', 'right' => 'Правильность'];

                        echo '<ul>';
                        foreach($question['answers'] as $aNum => $answer) {
                            $missingParams = array_diff(array_keys($answerParams), array_keys($answer));

                            if ($missingParams) {
                                $errCount++;
                                echo '<p class="alert">'.$aNum.'-й ответ не имеет следующих параметров</p><ul>';

                                foreach($missingParams as $param) {
                                    echo "<li>$answerParams[$param]</li>";
                                }

                                echo '</ul>';
                                continue;
                            }

                            //Answer info output
                            echo '<li'.($answer['right'] ? ' class="right">' : '>')
                                .$answer['content'].'</li>';
                        }
                        echo '</ul>';        

                        // Checking presence of right answers
                        $rights = array_column($question['answers'], 'right');
                        if(!in_array(true, $rights)) {
                            echo '<p class="alert">Для вопроса не указано ни одного правильного ответа</p>';
                        }
                    }
                }

                if($errCount) {
                    echo '<p class="alert"><strong>Ошибок в файле: '.$errCount.'</strong></p>';
                }
                else {
                    echo '<p class="success"><strong>Все тесты в порядке!</strong></p>';
                }

                return $errCount;
            }


            // Main code

            if (isset($_FILES['file'])) {

                $type = $_FILES['file']['type'];
                $tmpName = $_FILES['file']['tmp_name'];
                $name = $_FILES['file']['name'];
                $extension = array_pop(explode('.',$name));

                if ($type == "application/json" // for POSIX-compatible
                    || 
                    $extension == "json"        // for Windows
                    ) {

                    $errCount = checkTestsErrors($tmpName);

                    if (!$errCount) {
                        echo '<p class="success">Файл '.$name.' успешно загружен</p>';
                        move_uploaded_file($tmpName, 'tests/tests.json');
                    }
                    else {
                        echo '<p class="alert">Файл '.$name.' не загружен. Ошибок в файле:'.$errCount.'</p>';
                    }

                    
                }
                else {
                    echo '<p class="alert">Файл неверного типа или не выбран! Допускаются только файлы в формате json.</p>';
                }
            }
        ?>
    </div>
</body>
</html>