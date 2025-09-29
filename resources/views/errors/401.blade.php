<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 - Không có quyền truy cập</title>
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
            background: linear-gradient(135deg, var(--primary-color-4) 0%, var(--purple-color-2) 50%, var(--primary-color-2) 100%);
            background-attachment: fixed;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Animated Security Patterns */
        .security-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            z-index: 1;
        }

        .security-line {
            position: absolute;
            background: linear-gradient(45deg, var(--primary-color-3), var(--purple-color-1));
            animation: securityScan 4s linear infinite;
        }

        .security-line:nth-child(1) {
            width: 100%;
            height: 2px;
            top: 20%;
            animation-delay: 0s;
        }

        .security-line:nth-child(2) {
            width: 2px;
            height: 100%;
            left: 30%;
            animation-delay: 1s;
        }

        .security-line:nth-child(3) {
            width: 100%;
            height: 2px;
            top: 60%;
            animation-delay: 2s;
        }

        .security-line:nth-child(4) {
            width: 2px;
            height: 100%;
            left: 70%;
            animation-delay: 3s;
        }

        @keyframes securityScan {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            50% {
                opacity: 0.6;
                transform: scale(1);
            }
            100% {
                opacity: 0;
                transform: scale(1.2);
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
            background: linear-gradient(45deg, #ff6b6b, var(--primary-color-3), var(--purple-color-1));
            background-size: 200% 200%;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 
                4px 4px 0 rgba(255, 107, 107, 0.3),
                8px 8px 0 rgba(255, 107, 107, 0.2),
                12px 12px 0 rgba(255, 107, 107, 0.1);
            animation: errorPulse 2s ease-in-out infinite, gradientShift 3s ease-in-out infinite;
            margin-bottom: 2rem;
            transform-style: preserve-3d;
        }

        @keyframes errorPulse {
            0%, 100% {
                transform: scale(1) rotateY(0deg);
            }
            50% {
                transform: scale(1.05) rotateY(5deg);
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
                0 10px 20px rgba(255, 107, 107, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            transform-style: preserve-3d;
            animation: cardHover 6s ease-in-out infinite;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 107, 107, 0.2);
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 107, 107, 0.1), transparent);
            animation: securitySweep 4s infinite;
        }

        @keyframes cardHover {
            0%, 100% {
                transform: translateY(0) rotateX(0deg);
            }
            50% {
                transform: translateY(-8px) rotateX(1deg);
            }
        }

        @keyframes securitySweep {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }

        /* Security Lock Icon */
        .icon-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            transform-style: preserve-3d;
        }

        .security-icon {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ff6b6b, var(--primary-color-3));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            box-shadow: 
                0 10px 30px rgba(255, 107, 107, 0.4),
                0 5px 15px rgba(216, 107, 107, 0.3);
            animation: lockShake 3s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .security-icon::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: conic-gradient(#ff6b6b, var(--primary-color-3), #ff6b6b);
            border-radius: 50%;
            z-index: -1;
            animation: borderRotate 3s linear infinite;
        }

        .security-icon i {
            position: relative;
            z-index: 2;
            animation: lockBounce 2s ease-in-out infinite;
        }

        @keyframes lockShake {
            0%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(-5deg);
            }
            75% {
                transform: rotate(5deg);
            }
        }

        @keyframes lockBounce {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        @keyframes borderRotate {
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
            background: linear-gradient(45deg, #ff6b6b, var(--primary-color-3));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            animation: titleGlow 3s ease-in-out infinite;
        }

        .error-message {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
            opacity: 0;
            animation: fadeInUp 1s ease-out 0.5s forwards;
        }

        @keyframes titleGlow {
            0%, 100% {
                filter: brightness(1) drop-shadow(0 0 10px rgba(255, 107, 107, 0.3));
            }
            50% {
                filter: brightness(1.2) drop-shadow(0 0 20px rgba(255, 107, 107, 0.5));
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

        .login-btn {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            color: white;
            box-shadow: 
                0 10px 20px rgba(255, 107, 107, 0.3),
                0 5px 10px rgba(255, 142, 142, 0.2);
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

        .login-btn:hover {
            box-shadow: 
                0 20px 40px rgba(255, 107, 107, 0.4),
                0 10px 20px rgba(255, 142, 142, 0.3);
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
            
            .security-icon {
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
    <div class="security-pattern">
        <div class="security-line"></div>
        <div class="security-line"></div>
        <div class="security-line"></div>
        <div class="security-line"></div>
    </div>
    
    <div class="error-container">
        <div class="error-number">401</div>
        
        <div class="error-card">
            <div class="icon-container">
                <div class="security-icon">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
            
            <h1 class="error-title">Không có quyền truy cập</h1>
            
            <p class="error-message">
                Xin lỗi! Bạn không có quyền truy cập vào trang này. 
                Vui lòng đăng nhập hoặc liên hệ quản trị viên để được hỗ trợ.
            </p>
            
            <div class="buttons-container">
                <a href="{{ url('/') }}" class="action-btn home-btn">
                    <i class="fas fa-home"></i>
                    Về trang chủ
                </a>
                <a href="{{ route('login') }}" class="action-btn login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Đăng nhập
                </a>
            </div>
        </div>
    </div>
</body>
</html>