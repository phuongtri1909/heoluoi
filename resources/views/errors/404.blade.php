<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Trang không tồn tại</title>
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
            background: linear-gradient(135deg, var(--primary-color-2) 0%, var(--purple-color-2) 50%, var(--primary-color-1) 100%);
            background-attachment: fixed;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Animated Background Particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: float 6s infinite ease-in-out;
        }

        .particle:nth-child(1) {
            width: 80px;
            height: 80px;
            left: 10%;
            top: 20%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 60px;
            height: 60px;
            left: 80%;
            top: 80%;
            animation-delay: 1s;
        }

        .particle:nth-child(3) {
            width: 40px;
            height: 40px;
            left: 40%;
            top: 60%;
            animation-delay: 2s;
        }

        .particle:nth-child(4) {
            width: 100px;
            height: 100px;
            left: 70%;
            top: 20%;
            animation-delay: 3s;
        }

        .particle:nth-child(5) {
            width: 50px;
            height: 50px;
            left: 20%;
            top: 70%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.7;
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 1;
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
            background: linear-gradient(45deg, var(--primary-color-3), var(--purple-color-1), var(--primary-color-1));
            background-size: 200% 200%;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 
                4px 4px 0 rgba(216, 107, 107, 0.3),
                8px 8px 0 rgba(216, 107, 107, 0.2),
                12px 12px 0 rgba(216, 107, 107, 0.1);
            animation: gradientShift 3s ease-in-out infinite, bounce 2s ease-in-out infinite;
            margin-bottom: 2rem;
            transform-style: preserve-3d;
        }

        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0) rotateX(0deg);
            }
            40% {
                transform: translateY(-10px) rotateX(5deg);
            }
            60% {
                transform: translateY(-5px) rotateX(-5deg);
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
                0 10px 20px rgba(216, 107, 107, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            transform-style: preserve-3d;
            animation: cardFloat 6s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes cardFloat {
            0%, 100% {
                transform: translateY(0) rotateX(0deg);
            }
            50% {
                transform: translateY(-10px) rotateX(2deg);
            }
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }

        /* Icon Container */
        .icon-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            transform-style: preserve-3d;
        }

        .error-icon {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color-3), var(--purple-color-1));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            box-shadow: 
                0 10px 30px rgba(216, 107, 107, 0.3),
                0 5px 15px rgba(216, 160, 219, 0.2);
            animation: iconSpin 4s linear infinite;
            position: relative;
            overflow: hidden;
        }

        .error-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: rotate 2s linear infinite;
        }

        .error-icon i {
            position: relative;
            z-index: 2;
            animation: iconPulse 2s ease-in-out infinite;
        }

        @keyframes iconSpin {
            0% {
                transform: rotateY(0deg);
            }
            100% {
                transform: rotateY(360deg);
            }
        }

        @keyframes iconPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Typography */
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-color-3), var(--purple-color-1));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            animation: textGlow 3s ease-in-out infinite;
        }

        .error-message {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
            opacity: 0;
            animation: fadeInUp 1s ease-out 0.5s forwards;
        }

        @keyframes textGlow {
            0%, 100% {
                filter: brightness(1);
            }
            50% {
                filter: brightness(1.2);
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

        /* 3D Button */
        .home-btn {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: linear-gradient(135deg, var(--primary-color-3), var(--purple-color-1));
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 
                0 10px 20px rgba(216, 107, 107, 0.3),
                0 5px 10px rgba(216, 160, 219, 0.2);
            transform: translateY(0);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            opacity: 0;
            animation: fadeInUp 1s ease-out 1s forwards;
        }

        .home-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .home-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 
                0 20px 40px rgba(216, 107, 107, 0.4),
                0 10px 20px rgba(216, 160, 219, 0.3);
            color: white;
        }

        .home-btn:hover::before {
            left: 100%;
        }

        .home-btn:active {
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
            
            .error-icon {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
    
    <div class="error-container">
        <div class="error-number">404</div>
        
        <div class="error-card">
            <div class="icon-container">
                <div class="error-icon">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            
            <h1 class="error-title">Trang không tồn tại</h1>
            
            <p class="error-message">
                Xin lỗi! Trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển. 
                Hãy kiểm tra lại đường dẫn hoặc quay về trang chủ.
            </p>
            
            <a href="{{ url('/') }}" class="home-btn">
                <i class="fas fa-home me-2"></i>
                Về trang chủ
            </a>
        </div>
    </div>
</body>
</html>
