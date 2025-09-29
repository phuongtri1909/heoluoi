@push('styles')
    <style>
        .section-title {
            display: flex;
            align-items: center;
            gap: clamp(10px, 2vw, 20px);
            margin: clamp(1rem, 3vw, 2rem) 0;
            padding: 0 15px;
        }

        .section-title::before,
        .section-title::after {
            content: "";
            flex: 1;
            height: 1px;
            background-color: #dee2e6;
        }

        .section-title h5 {
            margin: 0;
            white-space: nowrap;
            color: #333;
            font-weight: 600;
            font-size: clamp(0.9rem, 2.5vw, 1.25rem);
        }

        @media (max-width: 576px) {
            .section-title {
                gap: 10px;
            }

            .section-title::before,
            .section-title::after {
                max-width: 60px;
            }

            .section-title h5 {
                text-align: center;
                white-space: normal;
                font-size: 0.9rem;
            }
        }

        .chapter-info {
            z-index: 1;
            text-align: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }


        .chapter-meta {
            font-size: clamp(0.7rem, 2vw, 0.9rem);
        }

        .book {
            width: clamp(180px, 100%, 225px);
            height: clamp(280px, 50vw, 350px);
            position: relative;
            text-align: center;
        }

        .book-cover {
            position: absolute;
            z-index: 1;
            width: 100%;
            height: 100%;
            transform-origin: 0 50%;
            background-size: cover;
            border-radius: 3px;
            box-shadow: inset 4px 1px 3px #ffffff60,
                inset 0 -1px 2px #00000080;
            transition: all .5s ease-in-out;
        }

        .cover3 {
            background: url('../assets/images/banner-book.webp');
        }

        .book .book-cover {
            background-size: 100% 100%;
        }


        .effect {
            width: 20px;
            height: 100%;
            margin-left: 10px;
            border-left: 2px solid #00000010;
            background-image: linear-gradient(90deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 100%);
            transition: all .5s ease;
        }

        .light {
            width: 90%;
            height: 100%;
            position: absolute;
            border-radius: 3px;
            background-image: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.2) 100%);
            top: 0;
            right: 0;
            opacity: .1;
            transition: all .5s ease;
            -webkit-transition: all .5s ease;
        }

        .book:hover {
            cursor: pointer;
        }

        .book:hover .book-cover {
            transform: perspective(2000px) rotateY(-30deg);
            -webkit-transform: perspective(2000px) rotateY(-30deg);
            transform-style: preserve-3d;
            -webkit-transform-style: preserve-3d;
            box-shadow:
                inset 4px 1px 3px #ffffff60,
                inset 0 -1px 2px #00000080,
                10px 0px 10px -5px #00000030
        }

        .book:hover .effect {
            width: 40px;
            /** margin-left:13px;
                                                      opacity: 0.5; **/
        }

        .book:hover .light {
            opacity: 1;
            width: 70%;
        }

        .book-inside {
            width: calc(100% - 2px);
            height: 96%;
            position: relative;
            top: 2%;
            border: 1px solid grey;
            border-radius: 3px;
            background: white;
            box-shadow:
                10px 40px 40px -10px #00000030,
                inset -2px 0 0 grey,
                inset -3px 0 0 #dbdbdb,
                inset -4px 0 0 white,
                inset -5px 0 0 #dbdbdb,
                inset -6px 0 0 white,
                inset -7px 0 0 #dbdbdb,
                inset -8px 0 0 white,
                inset -9px 0 0 #dbdbdb;
        }

        @media (max-width: 1200px) {
            .book {
                width: 200px;
                height: 310px;
            }
        }

        @media (max-width: 992px) {
            .book {
                width: 180px;
                height: 280px;
            }

            .effect {
                width: 15px;
            }

            .book:hover .effect {
                width: 30px;
            }
        }

        @media (max-width: 768px) {
            .book {
                width: 160px;
                height: 250px;
            }

            .book-inside {
                height: 94%;
            }
        }

        @media (max-width: 576px) {
            .book {
                width: 140px;
                height: 220px;
            }

            .chapter-info {
                width: 90%;
            }

            .chapter-meta .stat-item {
                margin-bottom: 5px;
            }
        }

        /* Touch Device Support */
        @media (hover: none) {

            .light {
                opacity: 0.5;
                width: 80%;
            }

            .effect {
                width: 30px;
            }
        }

        .chapter-title {
            font-family: 'ZCOOL XiaoWei', sans-serif;
        }
    </style>
@endpush

<div class="book">
    
    <div class="book-cover cover3">
        <div class="effect"></div>
        <div>
            <div class="light"></div>
        </div>
    </div>
    <div class="book-inside">
    </div>
</div>
