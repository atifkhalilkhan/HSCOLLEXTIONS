<?php
session_start();
$order_id = $_GET['order_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - HSCOLLEXTIONS</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f7f8fc, #eef2f3);
            font-family: 'Poppins', sans-serif;
        }
        .thank-you-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .thank-card {
            background: #fff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 90%;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            animation: fadeInUp 0.8s ease-in-out;
        }
        .thank-card .checkmark {
            font-size: 80px;
            color: #28a745;
            animation: pop 0.6s ease forwards;
        }
        .thank-card h2 {
            font-weight: 700;
            color: #222;
            margin-top: 20px;
        }
        .thank-card p {
            color: #666;
            margin-bottom: 30px;
            font-size: 15px;
        }
        .thank-card .btn-dark {
            background: #000;
            border-radius: 50px;
            padding: 10px 30px;
            transition: all 0.3s ease;
        }
        .thank-card .btn-dark:hover {
            background: #333;
            transform: scale(1.05);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pop {
            0% { transform: scale(0.3); opacity: 0; }
            80% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); }
        }

        /* Confetti animation (optional fun effect 🎉) */
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: #ffce00;
            animation: fall 3s linear infinite;
        }
        @keyframes fall {
            0% { transform: translateY(-10px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(400px) rotate(360deg); opacity: 0; }
        }
    </style>
</head>
<body>

    <section class="thank-you-page">
        <div class="thank-card">
            <div class="checkmark mb-3">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Thank You for Your Order!</h2>
            <p>
                Your order<?php if ($order_id) echo " <b>(#" . htmlspecialchars($order_id) . ")</b>"; ?> 
                has been placed successfully.<br>
                You’ll receive a confirmation email shortly.
            </p>
            <a href="index.php" style="color: white;" class="btn btn-dark">Continue Shopping</a>
        </div>
    </section>

    <!-- Optional Confetti 🎉 -->
    <script>
        for (let i = 0; i < 30; i++) {
            let confetti = document.createElement('div');
            confetti.classList.add('confetti');
            confetti.style.left = Math.random() * window.innerWidth + 'px';
            confetti.style.backgroundColor = ['#f44336','#e91e63','#9c27b0','#3f51b5','#2196f3','#4caf50','#ff9800'][Math.floor(Math.random()*7)];
            confetti.style.animationDelay = Math.random() * 2 + 's';
            document.body.appendChild(confetti);
        }
    </script>

</body>
</html>
