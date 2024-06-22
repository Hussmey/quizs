<?php
$servername = "127.0.0.1"; // اسم المضيف
$username = ""; // اسم المستخدم
$password = ""; // كلمة المرور
$dbname = "exam_online"; // اسم قاعدة البيانات

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
