<?php
error_reporting(0);
@include 'config.php';
session_start();
if (!isset($_SESSION['user_name']) || !isset($_GET['course_id'])) {
    die("Unauthorized access or missing course ID.");
}
$student_name = $_SESSION['user_name'];
$student_email = $_SESSION['user_email'] ?? '';
$course_id = intval($_GET['course_id']);
$course_query = @mysqli_query($conn, "SELECT name FROM products WHERE id = '$course_id'");
if(!$course_query || mysqli_num_rows($course_query) == 0) {
    die("Course not found.");
}
$course_data = mysqli_fetch_assoc($course_query);
$course_name = $course_data['name'];
$safe_filename = preg_replace('/[^A-Za-z0-9\-]/', '_', $course_name);
$issue_date = date("F j, Y");
$cert_id_string = "BOT-" . date("Y") . "-" . strtoupper(substr(md5($student_name . $course_id . "BOT_SALT"), 0, 8));
$user_id_check = intval($_SESSION['id'] ?? $_SESSION['user_id'] ?? 0);
$cert_query = @mysqli_query($conn, "SELECT issue_date FROM certificates WHERE course_id = '$course_id' AND user_id = '$user_id_check'");
if($cert_query && mysqli_num_rows($cert_query) > 0) {
    $cert_db = mysqli_fetch_assoc($cert_query);
    if(!empty($cert_db['issue_date'])) {
        $issue_date = date("F j, Y", strtotime($cert_db['issue_date']));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($student_name) ?> - Official BOT Certificate</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700;900&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Montserrat:wght@300;400;600&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        :root {
            --navy: #0f172a;
            --gold: #C5A059;
            --paper: #faf9f6;
        }

        body { 
            background-color: #cbd5e1; 
            font-family: 'Montserrat', sans-serif; 
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 2rem;
        }
        
        .font-cinzel { font-family: 'Cinzel Decorative', serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-script { font-family: 'Great Vibes', cursive; }
        
        /* Fixed A4 Landscape Container */
        .cert-canvas {
            background-color: var(--paper);
            background-image: url('https://www.transparenttextures.com/patterns/cream-dust.png');
            width: 297mm; /* A4 Width */
            height: 210mm; /* A4 Height */
            max-width: 100%;
            position: relative;
            box-sizing: border-box;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            overflow: hidden;
            flex-shrink: 0;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 350px;
            color: rgba(15, 23, 42, 0.04);
            z-index: 1;
            pointer-events: none;
        }

        .text-gold {
            background: linear-gradient(to right, #B38728, #D4AF37, #B38728);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: #C5A059; 
        }

        .outer-border { position: absolute; inset: 15px; border: 10px solid var(--navy); z-index: 2; pointer-events: none; }
        .inner-border { position: absolute; inset: 32px; border: 2px solid var(--gold); outline: 1px solid var(--gold); outline-offset: 4px; z-index: 2; pointer-events: none; }
        .corner { position: absolute; width: 45px; height: 45px; border: 3px solid var(--gold); background: var(--paper); z-index: 3; }
        .corner-tl { top: 22px; left: 22px; border-right: none; border-bottom: none; }
        .corner-tr { top: 22px; right: 22px; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 22px; left: 22px; border-right: none; border-top: none; }
        .corner-br { bottom: 22px; right: 22px; border-left: none; border-top: none; }

        
        @media print {
            @page { size: A4 landscape; margin: 0; }
            body { background: none; padding: 0; margin: 0; }
            .no-print { display: none !important; }
            .cert-canvas { box-shadow: none !important; margin: 0 auto; print-color-adjust: exact !important; -webkit-print-color-adjust: exact !important; }
            .outer-border { inset: 12px; } .inner-border { inset: 26px; }
            .corner-tl { top: 16px; left: 16px; } .corner-tr { top: 16px; right: 16px; }
            .corner-bl { bottom: 16px; left: 16px; } .corner-br { bottom: 16px; right: 16px; }
        }
    </style>
</head>
<body>
    <div class="no-print mb-8 flex flex-wrap justify-center gap-4 w-full max-w-[297mm]">   
        <button id="downloadBtn" onclick="downloadCertificate()" class="bg-[#0f172a] hover:bg-[#1e293b] text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-all flex items-center gap-3">
            <i class="fas fa-download text-[#C5A059]"></i> <span id="btnText">Download PDF</span>
        </button>
        <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-all flex items-center gap-2">
            <i class="fas fa-print"></i> Print
        </button>
        <a href="student_panel.php" class="bg-white hover:bg-gray-100 text-[#0f172a] font-bold py-3 px-8 rounded-xl shadow-lg transition-all flex items-center gap-2 border border-gray-300">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    <div id="certificateTarget" class="cert-canvas">        
        <i class="fas fa-university watermark"></i>
        <div class="outer-border"></div>
        <div class="inner-border"></div>
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>
        <div class="relative z-10 w-full h-full flex flex-col items-center justify-between py-12 px-20 text-center">           
            <div class="flex flex-col items-center mt-2">
                <i class="fas fa-graduation-cap text-4xl text-[#0f172a] mb-2"></i>
                <h3 class="font-montserrat tracking-[0.25em] text-[#C5A059] text-xs font-bold uppercase mb-1">The Board of BOT LEARNING</h3>
                <h2 class="font-cinzel text-2xl font-black text-[#0f172a] tracking-widest uppercase">BOT Learning</h2>
            </div>
            <div class="my-4">
                <h1 class="font-cinzel text-5xl font-black text-gold tracking-wider mb-2">
                    Certificate of Excellence
                </h1>
                <p class="font-script text-2xl text-gray-600">is proudly presented to</p>
            </div>
            <div class="w-full">
                <h2 class="font-playfair text-4xl italic text-[#0f172a] font-bold border-b-2 border-[#C5A059] pb-2 mb-3 px-10 inline-block w-max max-w-full overflow-hidden text-ellipsis whitespace-nowrap">
                    <?= htmlspecialchars($student_name) ?>
                </h2>
                <p class="font-montserrat text-xs tracking-wide text-gray-600 max-w-2xl mx-auto leading-relaxed uppercase">
                    In recognition of their outstanding dedication, proficiency, and the successful completion of the comprehensive curriculum for:
                </p>
            </div>
            <div class="my-2">
                <h3 class="font-cinzel text-2xl font-bold text-[#0f172a] max-w-3xl line-clamp-2">
                    <?= htmlspecialchars($course_name) ?>
                </h3>
            </div>
            <div class="w-full flex justify-between items-end px-4 mt-6 mb-2">
                <div class="text-center w-56">
                    <p class="font-playfair text-lg text-[#0f172a] font-bold mb-1"><?= $issue_date ?></p>
                    <div class="border-t-[2.5px] border-[#0f172a] w-full mb-1"></div>
                    <p class="font-montserrat text-[9px] tracking-widest uppercase text-gray-500 font-bold">Date of Issuance</p>
                </div>

                <div class="relative flex items-center justify-center transform translate-y-2">
                    <svg width="120" height="120" viewBox="0 0 200 200">
                        <path d="M100 0 L110 15 L128 10 L134 26 L151 25 L153 42 L169 45 L166 61 L180 68 L172 83 L183 93 L172 104 L180 119 L166 126 L169 142 L153 145 L151 162 L134 161 L128 177 L110 172 L100 187 L90 172 L72 177 L66 161 L49 162 L51 145 L31 142 L34 126 L20 119 L28 104 L17 93 L28 83 L20 68 L34 61 L31 45 L51 42 L49 25 L66 26 L72 10 L90 15 Z" fill="#D4AF37" stroke="#B8860B" stroke-width="1.5"/>
                        <circle cx="100" cy="93.5" r="70" fill="#DAA520" stroke="#FFF8DC" stroke-width="2"/>
                        <circle cx="100" cy="93.5" r="62" fill="none" stroke="#FFF8DC" stroke-width="1" stroke-dasharray="4 4"/>
                        <text x="100" y="80" font-family="Arial, sans-serif" font-size="14" font-weight="bold" fill="#FFF8DC" text-anchor="middle" letter-spacing="2">OFFICIAL</text>
                        <text x="100" y="102" font-family="'Times New Roman', serif" font-size="28" font-weight="900" fill="#FFF8DC" text-anchor="middle">BOT</text>
                        <text x="100" y="120" font-family="Arial, sans-serif" font-size="10" font-weight="bold" fill="#FFF8DC" text-anchor="middle" letter-spacing="3">SEAL</text>
                    </svg>
                </div>
                <div class="text-center w-56">
                    <div class="font-script text-2xl text-[#0f172a] transform -rotate-2 mb-1">BOT &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Director</div>
                    <div class="border-t-[2.5px] border-[#0f172a] w-full mb-1"></div>
                    <p class="font-montserrat text-[9px] tracking-widest uppercase text-gray-500 font-bold">Authorized Signature</p>
                </div>
            </div>
            <div class="absolute bottom-4 left-0 w-full text-center">
                <p class="font-montserrat text-[8px] text-gray-400 tracking-widest uppercase">
                    Verify Authenticity at <span class="font-bold text-gray-500">botlearning.edu/verify</span> &nbsp;|&nbsp; 
                    Certificate ID: <span class="font-bold text-gray-500"><?= $cert_id_string ?></span>
                </p>
            </div>
        </div>
    </div>

    <script>
        function downloadCertificate() {
            const btn = document.getElementById('downloadBtn');
            const btnText = document.getElementById('btnText');
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btnText.innerHTML = 'Generating PDF... <i class="fas fa-spinner fa-spin ml-1"></i>';
            const element = document.getElementById('certificateTarget');
            const fileName = 'BOT_Certificate_<?= $safe_filename ?>.pdf';
            const opt = {
                margin: 0,
                filename: fileName,
                image: { type: 'jpeg', quality: 1 },
                html2canvas: { 
                    scale: 2, 
                    useCORS: true, 
                    letterRendering: true,
                    logging: false
                },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(opt).from(element).save().then(function() {
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                btnText.innerHTML = 'Download PDF';
            });
        }
    </script>
</body>
</html>
