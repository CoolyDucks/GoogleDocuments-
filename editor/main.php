<?php
$icons = "../img/";
$contact = "Coolyducks@proton.me";

echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Google Documents</title>
    <style>
        :root { --g-blue: #0b57d0; --g-red: #db4437; --shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24); --fab-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23); }
        body { margin: 0; font-family: 'Segoe UI', Roboto, sans-serif; background: #f8f9fa; color: #1f1f1f; transition: all 0.3s; }
        .main-screen { height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        .fab { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); width: 65px; height: 65px; background: #000; border-radius: 18px; display: flex; justify-content: center; align-items: center; cursor: pointer; color: #fff; font-size: 35px; z-index: 100; box-shadow: var(--fab-shadow); transition: 0.3s cubic-bezier(.25,.8,.25,1); }
        .fab:hover { transform: translateX(-50%) scale(1.05); background: #222; }
        .fab-menu { display: none; position: fixed; bottom: 110px; left: 50%; transform: translateX(-50%); background: #fff; padding: 20px; border-radius: 24px; box-shadow: var(--fab-shadow); gap: 25px; z-index: 99; animation: slideUp 0.3s ease; }
        @keyframes slideUp { from { opacity: 0; bottom: 80px; } to { opacity: 1; bottom: 110px; } }
        .fab-menu img { width: 55px; height: 55px; cursor: pointer; transition: 0.2s; }
        .fab-menu img:hover { transform: translateY(-5px); }
        .feedback-btn { position: fixed; top: 25px; right: 25px; cursor: pointer; transition: 0.2s; }
        .feedback-btn:hover { opacity: 0.7; }
        .feedback-btn img { width: 40px; height: 40px; }
        #editor-container { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #fff; z-index: 200; flex-direction: column; animation: fadeIn 0.4s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .toolbar { padding: 12px 25px; background: #fff; border-bottom: 1px solid #e0e0e0; display: flex; gap: 15px; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .toolbar button { border: none; background: transparent; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-weight: 500; color: #444; transition: 0.2s; }
        .toolbar button:hover { background: #f1f3f4; }
        #canvas { flex-grow: 1; padding: 60px; outline: none; overflow-y: auto; width: 210mm; margin: 15px auto; background: #fff; box-shadow: var(--shadow); border: 1px solid #ddd; min-height: 297mm; }
        .modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 30px; border-radius: 28px; box-shadow: var(--fab-shadow); z-index: 300; text-align: center; width: 300px; }
        input[type="text"] { padding: 12px; width: 100%; box-sizing: border-box; border-radius: 8px; border: 1px solid #dadce0; margin-bottom: 20px; outline: none; }
        input[type="text"]:focus { border: 2px solid var(--g-blue); }
        .btn-primary { background: var(--g-blue); color: #fff; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; }
    </style>
</head>
<body>

    <div id="mainScreen" class="main-screen">
        <h1 style="font-size: 3rem;">Google Documents</h1>
        <div class="feedback-btn" onclick="location.href='mailto:$contact'">
            <img src="{$icons}feedback.png">
        </div>
        <div id="fabMenu" class="fab-menu">
            <img src="{$icons}word.png" onclick="askFileName('docx')">
            <img src="{$icons}pdf.png" onclick="askFileName('pdf')">
        </div>
        <div class="fab" onclick="toggleMenu()">+</div>
    </div>

    <div id="nameModal" class="modal">
        <h3>New Document</h3>
        <input type="text" id="fileNameInput" placeholder="Enter file name...">
        <button class="btn-primary" onclick="startEditor()">Create</button>
        <button style="background:none; border:none; color:#5f6368; cursor:pointer;" onclick="document.getElementById('nameModal').style.display='none'">Cancel</button>
    </div>

    <div id="editor-container">
        <div class="toolbar">
            <button onclick="exec('formatBlock', 'H1')" style="color:var(--g-blue)">Title</button>
            <button onclick="exec('bold')"><b>B</b></button>
            <button onclick="exec('fontSize', '3')">Soft</button>
            <button onclick="exec('fontSize', '6')">Large</button>
            <input type="color" onchange="exec('foreColor', this.value)" style="border:none; width:30px; height:30px; cursor:pointer;">
            <label for="imgInp" style="cursor:pointer; background:#f1f3f4; padding:8px 12px; border-radius:8px;">Import Image</label>
            <input type="file" id="imgInp" style="display:none" onchange="readImg(this)">
            <button onclick="saveFile()" style="background:var(--g-blue); color:#fff; margin-left:auto;">Download</button>
            <button onclick="location.reload()" style="background:var(--g-red); color:#fff;">Exit</button>
        </div>
        <div id="canvas" contenteditable="true"></div>
    </div>

    <script>
        let currentType = '';
        function toggleMenu() {
            let m = document.getElementById('fabMenu');
            m.style.display = (m.style.display === 'flex') ? 'none' : 'flex';
        }
        function askFileName(t) {
            currentType = t;
            document.getElementById('fabMenu').style.display = 'none';
            document.getElementById('nameModal').style.display = 'block';
        }
        function startEditor() {
            let n = document.getElementById('fileNameInput').value;
            if(!n) return;
            document.getElementById('nameModal').style.display = 'none';
            document.getElementById('mainScreen').style.display = 'none';
            document.getElementById('editor-container').style.display = 'flex';
        }
        function exec(c, v = null) { document.execCommand(c, false, v); }
        function readImg(input) {
            if (input.files && input.files[0]) {
                let r = new FileReader();
                r.onload = function(e) {
                    let i = document.createElement('img');
                    i.src = e.target.result; i.style.maxWidth = '100%';
                    document.getElementById('canvas').appendChild(i);
                };
                r.readAsDataURL(input.files[0]);
            }
        }
        function saveFile() {
            let f = document.createElement('form');
            f.method = 'POST'; f.action = '../stream.php';
            let c = document.createElement('input');
            c.type = 'hidden'; c.name = 'content'; c.value = document.getElementById('canvas').innerHTML;
            let n = document.createElement('input');
            n.type = 'hidden'; n.name = 'filename'; n.value = document.getElementById('fileNameInput').value + '.' + currentType;
            f.appendChild(c); f.appendChild(n);
            document.body.appendChild(f);
            f.submit();
        }
    </script>
</body>
</html>
HTML;

