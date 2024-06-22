<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>الاختبار</title>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5 mb-4">الاختبار</h1>
        <?php
        include 'db_config.php';

        if (isset($_GET['subject_id'])) {
            $subject_id = intval($_GET['subject_id']);
            echo "<form action='submit_exam.php' method='post'>";
            echo "<input type='hidden' name='subject_id' value='$subject_id'>";

            $sql = "SELECT q.id AS question_id, q.question_text 
                    FROM questions q
                    WHERE q.subject_id = $subject_id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $question_id = $row['question_id'];
                    $question_text = $row['question_text'];
                    echo "<div class='mb-3'>";
                    echo "<label for='question_$question_id' class='form-label'>$question_text</label>";

                    $sql_options = "SELECT id, option_text FROM options WHERE question_id = $question_id";
                    $result_options = $conn->query($sql_options);

                    if ($result_options->num_rows > 0) {
                        echo "<select name='answers[$question_id]' id='question_$question_id' class='form-select' required>";
                        echo "<option value='' selected disabled>اختر إجابتك</option>";
                        while ($row_option = $result_options->fetch_assoc()) {
                            $option_id = $row_option['id'];
                            $option_text = $row_option['option_text'];
                            echo "<option value='$option_id'>$option_text</option>";
                        }
                        echo "</select>";
                    } else {
                        echo "<p class='text-danger'>لا توجد خيارات متاحة لهذا السؤال.</p>";
                    }
                    echo "</div>";
                }
                echo "<button type='submit' class='btn btn-primary'>إرسال</button>";
            } else {
                echo "<p class='text-danger'>لا توجد أسئلة متاحة لهذه المادة.</p>";
            }
        } else {
            echo "<p class='text-danger'>لم يتم اختيار أي مادة.</p>";
        }

        $conn->close();
        ?>
    </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
