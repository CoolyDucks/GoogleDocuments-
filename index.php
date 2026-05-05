<?php
$icons = "img/";
$contact = "Coolyducks@proton.me";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Documents</title>
    <style>
        :root {
            --md-sys-color-primary: #0b57d0;
            --md-sys-color-surface: #f8f9fa;
            --md-sys-color-on-surface: #1f1f1f;
            --shadow-1: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            --shadow-2: 0 10px 20px rgba(0,0,0,0.15);
        }

        body, html { margin: 0; padding: 0; height: 100%; font-family: 'Segoe UI', Roboto, sans-serif; background: var(--md-sys-color-surface); overflow: hidden; }

        #welcome-screen {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #fff; display: flex; flex-direction: column;
            justify-content: center; align-items: center; text-align: center; z-index: 2000;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #welcome-screen h1 { color: #1f1f1f; font-size: 3.5em; margin: 0; }
        #welcome-screen h2 { color: #4285F4; font-size: 1.5em; margin-bottom: 20px; }
        #welcome-screen p { color: #5f6368; max-width: 500px; margin: 20px auto; font-size: 1.1em; }
        .start-btn { padding: 15px 45px; background: #000; color: #fff; border: none; border-radius: 50px; cursor: pointer; font-size: 1.1em; transition: 0.3s; }
        .start-btn:hover { transform: translateY(-3px); box-shadow: var(--shadow-2); }

        #main-app { display: none; opacity: 0; transition: opacity 0.5s ease; }

        header { padding: 15px 25px; display: flex; align-items: center; justify-content: space-between; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .search-box { background: #edf2fa; border-radius: 28px; padding: 10px 20px; width: 40%; display: flex; align-items: center; }
        .search-box input { border: none; background: transparent; outline: none; width: 100%; font-size: 16px; margin-left: 10px; }
        .settings-btn { cursor: pointer; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .settings-btn:hover { background: #e9eef6; }
        .settings-btn img { width: 24px; height: 24px; }

        #feedbackMenu { display: none; position: absolute; top: 60px; left: 20px; background: #fff; border-radius: 12px; box-shadow: var(--shadow-2); padding: 10px; z-index: 100; }

        main { padding: 40px; text-align: center; }
        .doc-placeholder { margin-top: 100px; opacity: 0.6; }

        .fab-container { position: fixed; bottom: 30px; left: 30px; display: flex; flex-direction: column-reverse; align-items: center; gap: 15px; }
        .fab-main { width: 56px; height: 56px; background: #fff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-2); cursor: pointer; }
        .fab-main span { font-size: 30px; color: var(--md-sys-color-primary); }

        .fab-options { display: none; flex-direction: column; gap: 10px; }
        .option-btn { background: #fff; padding: 10px; border-radius: 12px; box-shadow: var(--shadow-1); cursor: pointer; }
        .option-btn img { width: 35px; height: 35px; }

        .modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 24px; border-radius: 28px; box-shadow: var(--shadow-2); z-index: 1000; width: 300px; }
        .modal input { width: 100%; padding: 12px; margin: 15px 0; border: 1px solid #747775; border-radius: 8px; box-sizing: border-box; }
        .btn-done { background: var(--md-sys-color-primary); color: #fff; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; }
    </style>
</head>
<body>

<div id="welcome-screen">
    <h1>Google Documents</h1>
    <h2>PHP Edition</h2>
    <p>Experience the ultimate speed with our vanilla engine. No libraries, no bloat, just pure performance.</p>
    <button class="start-btn" onclick="startApp()">Get Started</button>
</div>

<div id="main-app">
    <header>
        <div class="settings-btn" onclick="toggleFeedback()">
            <img src="<?= $icons ?>settings.png" alt="Settings">
        </div>
        <div class="search-box">
            <span>🔍</span>
            <input type="text" placeholder="Search your documents...">
        </div>
        <div style="width: 40px;"></div>
    </header>

    <div id="feedbackMenu">
        <div onclick="alert('Contact: <?= $contact ?>')" style="cursor:pointer; display:flex; align-items:center; gap:10px;">
            <img src="<?= $icons ?>feedback.png" style="width:24px;">
            <span>Feedback</span>
        </div>
    </div>

    <main>
        <div class="doc-placeholder">
            <h1 style="font-weight: 300; font-size: 40px;">Google Documents</h1>
            <p>No documents yet. Click the + button to start.</p>
        </div>
    </main>

    <div class="fab-container">
        <div class="fab-main" onclick="toggleFab()"><span>+</span></div>
        <div class="fab-options" id="fabOptions">
            <div class="option-btn" onclick="openNamingModal('docx')"><img src="<?= $icons ?>docx.png"></div>
            <div class="option-btn" onclick="openNamingModal('pdf')"><img src="<?= $icons ?>pdf.png"></div>
        </div>
    </div>
</div>

<div id="namingModal" class="modal">
    <h3>New Document</h3>
    <input type="text" id="docName" placeholder="Enter document name...">
    <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button onclick="closeModal()" style="background:none; border:none; cursor:pointer; color:#5f6368;">Cancel</button>
        <button class="btn-done" onclick="goToEditor()">Done</button>
    </div>
</div>

<script>
    let selectedType = '';

    function startApp() {
        const welcome = document.getElementById('welcome-screen');
        const app = document.getElementById('main-app');
        welcome.style.transform = 'translateY(-100%)';
        setTimeout(() => {
            welcome.style.display = 'none';
            app.style.display = 'block';
            setTimeout(() => app.style.opacity = '1', 50);
        }, 600);
    }

    function toggleFeedback() {
        const menu = document.getElementById('feedbackMenu');
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }

    function toggleFab() {
        const opts = document.getElementById('fabOptions');
        opts.style.display = (opts.style.display === 'flex') ? 'none' : 'flex';
    }

    function openNamingModal(type) {
        selectedType = type;
        document.getElementById('namingModal').style.display = 'block';
        document.getElementById('fabOptions').style.display = 'none';
    }

    function closeModal() { document.getElementById('namingModal').style.display = 'none'; }

    function goToEditor() {
        const name = document.getElementById('docName').value;
        if (!name) return alert("Please enter a name");
        window.location.href = `editor/main.php?name=${encodeURIComponent(name)}&type=${selectedType}`;
    }
</script>

</body>
</html>

