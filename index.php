<?php
// Xử lý request AJAX lấy direct link
if (isset($_GET['get_link']) && isset($_GET['url'])) {
    $url = $_GET['url'];
    $html = @file_get_contents($url);

    header('Content-Type: application/json');

    if (!$html) {
        echo json_encode(["error" => "Không thể truy cập MediaFire"]);
        exit;
    }

    // Tìm direct link trong HTML
    if (preg_match('/href="(https?:\/\/download[^"]+)"/', $html, $matches)) {
        echo json_encode(["directLink" => $matches[1]]);
    } else {
        echo json_encode(["error" => "Không tìm thấy direct link"]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tải file MediaFire trực tiếp</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #4f46e5;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        button {
            padding: 10px 20px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background: #4338ca;
        }
        .result {
            margin-top: 15px;
        }
        .result a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background: #10b981;
            color: white;
            border-radius: 8px;
            text-decoration: none;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>MediaFire Direct Downloader</h2>
        <input type="text" id="mediafireLink" placeholder="Nhập link MediaFire...">
        <button onclick="getDirectLink()">Lấy link tải</button>
        <div class="result" id="result"></div>
    </div>

    <script>
        async function getDirectLink() {
            const link = document.getElementById("mediafireLink").value.trim();
            const resultDiv = document.getElementById("result");

            if (!link) {
                resultDiv.innerHTML = "<p class='error'>Vui lòng nhập link MediaFire!</p>";
                return;
            }

            resultDiv.innerHTML = "⏳ Đang lấy link...";

            try {
                const response = await fetch(`?get_link=1&url=${encodeURIComponent(link)}`);
                const data = await response.json();

                if (data.directLink) {
                    resultDiv.innerHTML = `
                        ✅ Link tải trực tiếp:<br>
                        <a href="${data.directLink}" target="_blank">📥 Tải ngay</a>
                    `;
                } else {
                    resultDiv.innerHTML = `<p class='error'>❌ ${data.error}</p>`;
                }
            } catch (e) {
                resultDiv.innerHTML = `<p class='error'>Lỗi: ${e.message}</p>`;
            }
        }
    </script>
</body>
</html>