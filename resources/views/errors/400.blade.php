<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>400 - Yêu cầu không hợp lệ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color-1: #f79489;
            --primary-color-2: #fccfd4;
            --primary-color-3: #d86b6b;
            --primary-color-4: #fbd9d9;
            --primary-color-5: #f9b7bb;
            --purple-color-1: #d8a0db;
            --purple-color-2: #e9c9f9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Segoe UI Variable', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
            height: 100vh;
            background: linear-gradient(135deg, var(--primary-color-5) 0%, var(--purple-color-1) 50%, var(--primary-color-3) 100%);
            background-attachment: fixed;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Animated Error Patterns */
        .error-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.15;
            z-index: 1;
        }

        .error-wave {
            position: absolute;
            width: 100%;
            height: 100px;
            background: linear-gradient(45deg, var(--primary-color-3), var(--purple-color-1));
            clip-path: polygon(0 0, 100% 0, 100% 50%, 0 100%);
            animation: waveMove 4s ease-in-out infinite;
        }

        .error-wave:nth-child(1) {
            top: 10%;
            animation-delay: 0s;
        }

        .error-wave:nth-child(2) {
            top: 40%;
            animation-delay: 1s;
            clip-path: polygon(0 50%, 100% 0, 100% 100%, 0 50%);
        }

        .error-wave:nth-child(3) {
            top: 70%;
            animation-delay: 2s;
            clip-path: polygon(0 100%, 100% 50%, 100% 100%, 0 0);
        }

        @keyframes waveMove {
            0%, 100% {
                transform: translateX(0) scaleY(1);
                opacity: 0.15;
            }
            50% {
                transform: translateX(20px) scaleY(1.2);
                opacity: 0.25;
            }
        }

        /* Main Container */
        .error-container {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 2rem;
            max-width: 600px;
            width: 100%;
        }

        /* 3D Error Number */
        .error-number {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(45deg, #ff7675, var(--primary-color-3), var(--purple-color-1));
            background-size: 200% 200%;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 
                4px 4px 0 rgba(255, 118, 117, 0.3),
                8px 8px 0 rgba(255, 118, 117, 0.2),
                12px 12px 0 rgba(255, 118, 117, 0.1);
            animation: numberWobble 3s ease-in-out infinite, gradientShift 4s ease-in-out infinite;
            margin-bottom: 2rem;
            transform-style: preserve-3d;
        }

        @keyframes numberWobble {
            0%, 100% {
                transform: rotate(0deg) scale(1);
            }
            25% {
                transform: rotate(-2deg) scale(1.02);
            }
            75% {
                transform: rotate(2deg) scale(0.98);
            }
        }

        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        /* 3D Card Container */
        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 3rem 2rem;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1),
                0 10px 20px rgba(255, 118, 117, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            transform-style: preserve-3d;
            animation: cardTilt 5s ease-in-out infinite;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 118, 117, 0.2);
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 118, 117, 0.1), transparent);
            animation: errorScan 3s infinite;
        }

        @keyframes cardTilt {
            0%, 100% {
                transform: perspective(1000px) rotateX(0deg) rotateY(0deg);
            }
            33% {
                transform: perspective(1000px) rotateX(2deg) rotateY(-1deg);
            }
            66% {
                transform: perspective(1000px) rotateX(-1deg) rotateY(2deg);
            }
        }

        @keyframes errorScan {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }

        /* Warning Icon */
        .icon-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            transform-style: preserve-3d;
        }

        .warning-icon {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ff7675, var(--primary-color-1));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            box-shadow: 
                0 10px 30px rgba(255, 118, 117, 0.4),
                0 5px 15px rgba(247, 148, 137, 0.3);
            animation: warningPulse 2s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .warning-icon::before {
            content: '';
            position: absolute;
            inset: -3px;
            background: conic-gradient(from 0deg, #ff7675, var(--primary-color-1), #ff7675);
            border-radius: 50%;
            z-index: -1;
            animation: warningBorder 3s linear infinite;
        }

        .warning-icon::after {
            content: '';
            position: absolute;
            top: 20%;
            left: 20%;
            right: 20%;
            bottom: 20%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            animation: innerGlow 2s ease-in-out infinite;
        }

        .warning-icon i {
            position: relative;
            z-index: 2;
            animation: iconBlink 1.5s ease-in-out infinite;
        }

        @keyframes warningPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 
                    0 10px 30px rgba(255, 118, 117, 0.4),
                    0 5px 15px rgba(247, 148, 137, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 
                    0 15px 40px rgba(255, 118, 117, 0.5),
                    0 8px 20px rgba(247, 148, 137, 0.4);
            }
        }

        @keyframes warningBorder {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes innerGlow {
            0%, 100% {
                opacity: 0.3;
            }
            50% {
                opacity: 0.7;
            }
        }

        @keyframes iconBlink {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        /* Typography */
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, #ff7675, var(--primary-color-3));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            animation: titleShake 4s ease-in-out infinite;
        }

        .error-message {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
            opacity: 0;
            animation: fadeInUp 1s ease-out 0.5s forwards;
        }

        @keyframes titleShake {
            0%, 100% {
                filter: brightness(1);
                transform: translateX(0);
            }
            25% {
                filter: brightness(1.1);
                transform: translateX(-2px);
            }
            75% {
                filter: brightness(1.1);
                transform: translateX(2px);
            }
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Buttons Container */
        .buttons-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            opacity: 0;
            animation: fadeInUp 1s ease-out 1s forwards;
        }

        /* 3D Buttons */
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transform: translateY(0);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .home-btn {
            background: linear-gradient(135deg, var(--primary-color-3), var(--purple-color-1));
            color: white;
            box-shadow: 
                0 10px 20px rgba(216, 107, 107, 0.3),
                0 5px 10px rgba(216, 160, 219, 0.2);
        }

        .refresh-btn {
            background: linear-gradient(135deg, #ff7675, var(--primary-color-1));
            color: white;
            box-shadow: 
                0 10px 20px rgba(255, 118, 117, 0.3),
                0 5px 10px rgba(247, 148, 137, 0.2);
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .action-btn:hover {
            transform: translateY(-5px) scale(1.05);
            color: white;
        }

        .home-btn:hover {
            box-shadow: 
                0 20px 40px rgba(216, 107, 107, 0.4),
                0 10px 20px rgba(216, 160, 219, 0.3);
        }

        .refresh-btn:hover {
            box-shadow: 
                0 20px 40px rgba(255, 118, 117, 0.4),
                0 10px 20px rgba(247, 148, 137, 0.3);
        }

        .action-btn:hover::before {
            left: 100%;
        }

        .action-btn:active {
            transform: translateY(-2px) scale(1.02);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .error-number {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 2rem;
            }
            
            .error-message {
                font-size: 1rem;
            }
            
            .error-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .buttons-container {
                flex-direction: column;
                align-items: center;
            }
        }

        @media (max-width: 480px) {
            .error-number {
                font-size: 4rem;
            }
            
            .error-title {
                font-size: 1.5rem;
            }
            
            .icon-container {
                width: 80px;
                height: 80px;
            }
            
            .warning-icon {
                font-size: 2rem;
            }
            
            .action-btn {
                padding: 0.8rem 1.5rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-pattern">
        <div class="error-wave"></div>
        <div class="error-wave"></div>
        <div class="error-wave"></div>
    </div>
    
    <div class="error-container">
        <div class="error-number">400</div>
        
        <div class="error-card">
            <div class="icon-container">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            
            <h1 class="error-title">Yêu cầu không hợp lệ</h1>
            
            <p class="error-message">
                Xin lỗi! Yêu cầu của bạn không hợp lệ hoặc có lỗi cú pháp. 
                Vui lòng kiểm tra lại thông tin và thử lại.
            </p>
            
            <div class="buttons-container">
                <a href="{{ url('/') }}" class="action-btn home-btn">
                    <i class="fas fa-home"></i>
                    Về trang chủ
                </a>
                <a href="javascript:location.reload()" class="action-btn refresh-btn">
                    <i class="fas fa-redo-alt"></i>
                    Thử lại
                </a>
            </div>
        </div>
    </div>
</body>
</html>