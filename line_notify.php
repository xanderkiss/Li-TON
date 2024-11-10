<?php
// รับค่า tokens ที่เลือกมาจากฟอร์ม
$tokens = isset($_POST['tokens']) ? $_POST['tokens'] : [];

// ตรวจสอบว่ามีการเลือก token หรือไม่
if (!empty($tokens)) {
    foreach ($tokens as $token) {
        // ตั้งค่า token LINE ที่ใช้
        $line_token = $token;

        // ตั้งค่า header สำหรับส่งไปยัง LINE Notify
        $header = array(
            "Content-Type: multipart/form-data",
            "Authorization: Bearer " . $line_token
        );

        // รับข้อความจากฟอร์ม
        $message = $_POST['message'];

        // จัดการไฟล์ภาพถ้ามีการอัพโหลด
        $image_path = "";
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $image_path = $_FILES['image']['tmp_name'];
        }

        // กำหนดข้อมูลสำหรับการส่ง POST ไปยัง LINE Notify
        $data = array(
            'message' => $message
        );
        if ($image_path) {
            $data['imageFile'] = curl_file_create($image_path);
        }

        // ตั้งค่า cURL สำหรับการส่งข้อมูลไปยัง LINE Notify
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // ส่งข้อมูลและรับการตอบกลับ
        $result = curl_exec($ch);

        // ตรวจสอบผลลัพธ์
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);
    }

    // แสดงข้อความแจ้งเตือนและรีเฟรชหน้าโดยใช้ JavaScript
    echo "<script>
        alert('จัดส่งข้อความสำเร็จแล้ว');
        window.location.href = 'index.html';
    </script>";
} else {
    echo "<script>
        alert('กรุณาเลือก Token อย่างน้อยหนึ่งรายการ');
        window.location.href = 'index.html';
    </script>";
}
?>
