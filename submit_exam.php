<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>نتيجة الاختبار</title>
    <style>
        body {
            background-color: #f8f9fa; /* Bootstrap body background color */
        }
        .result-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .badge-info {
            background-color: #0d6efd;
        }
        .bg-red {
            background-color: #dc3545;
        }
        .border-success {
            border-color: #198754 !important;
        }
        .border-danger {
            border-color: #dc3545 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="result-container">
            <?php
            include 'db_config.php';
            session_start();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['subject_id'], $_POST['answers'])) {
                    $subject_id = intval($_POST['subject_id']);
                    $answers = $_POST['answers'];

                    $stmt = $conn->prepare("INSERT INTO exams (user_id, subject_id) VALUES (?, ?)");
                    $user_id = 1; 
                    $stmt->bind_param("ii", $user_id, $subject_id);
                    $stmt->execute();
                    $exam_id = $stmt->insert_id; 
                    $stmt->close();

                    $score = 0;
                    $feedback = [];

                    foreach ($answers as $question_id => $selectedOptionId) {
                        $sql = "SELECT is_correct, option_text 
                                FROM options 
                                WHERE question_id = ? AND id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $question_id, $selectedOptionId);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if (!$result) {
                            die("Error in SQL query: " . $conn->error); 
                        }

                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            if ($row['is_correct'] == 1) {
                                $score++;
                                $feedback[$question_id] = "<span class='badge badge-info'>إجابة صحيحة</span>: " . $row['option_text'];
                            } else {
                                $sql_correct = "SELECT option_text 
                                                FROM options 
                                                WHERE question_id = ? AND is_correct = 1";
                                $stmt_correct = $conn->prepare($sql_correct);
                                $stmt_correct->bind_param("i", $question_id);
                                $stmt_correct->execute();
                                $result_correct = $stmt_correct->get_result();

                                if ($result_correct->num_rows > 0) {
                                    $row_correct = $result_correct->fetch_assoc();
                                    $correct_answer = $row_correct['option_text'];
                                } else {
                                    $correct_answer = "لم يتم العثور على الإجابة الصحيحة.";
                                }

                                $feedback[$question_id] = "<span class='badge bg-red text-white'>اجابة خاطئة</span>: " . $row['option_text'] . " - الإجابة الصحيحة: " . $correct_answer;
                            }
                        } else {
                            $feedback[$question_id] = "لا توجد بيانات للاختيارات.";
                        }
                        $stmt->close();
                    }

                    $sql = "INSERT INTO results (exam_id, score) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $exam_id, $score);
                    $stmt->execute();
                    $stmt->close();

                    $percentage = round(($score / count($answers)) * 100, 2);

                    echo "<h2 class='text-center'>تم إرسال إجاباتك</h2>";
                    echo "<div class='text-center mb-4'>";
                    echo "<p>نتيجتك هي: $score من " . count($answers) . " </p>";
                    echo "<div class='progress' style='height: 25px;'>";
                    echo "<div class='progress-bar bg-success' role='progressbar' style='width: $percentage%;' aria-valuenow='$percentage' aria-valuemin='0' aria-valuemax='100'>$percentage%</div>";
                    echo "</div>";
                    echo "</div>";
                    
                    echo "<div class='mb-4'>";
                    foreach ($feedback as $question_id => $message) {
                        $correct_answer = strpos($message, "إجابة صحيحة") !== false;
                        $border_class = $correct_answer ? 'border-success' : 'border-danger';

                        echo "<p class='border p-3 $border_class'>$message</p>";
                    }
                    echo "</div>";
                } else {
                    echo "<p class='text-danger text-center'>لم يتم تقديم أي إجابات أو البيانات غير صالحة.</p>";
                }
            } else {
                echo "<p class='text-danger text-center'>طلب غير صالح.</p>";
            }

            $conn->close();
            ?>
            <div class="text-center">
                <a href="index.php" class="btn btn-primary">العودة إلى الصفحة الرئيسية</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
