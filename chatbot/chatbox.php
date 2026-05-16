<div class="magi-chat-container">


    <!-- CHAT BODY -->
    <div id="magi-chat-body"></div>

    <!-- QUICK SUGGESTIONS -->
    <div class="magi-suggestions">

        <button onclick="quickAsk('Suggest MBA universities in Canada')">
            Canada MBA
        </button>

        <button onclick="quickAsk('Scholarships in Australia')">
            Scholarships
        </button>

        <button onclick="quickAsk('Best IT courses in UK')">
            IT Courses
        </button>

    </div>

    <!-- FOOTER -->
    <div class="magi-footer">

        <label for="magi-file-upload" class="magi-upload-btn">
			📎
		</label>

		<input type="file" id="magi-file-upload" hidden>

        <input type="text"
               id="magi-user-input"
               placeholder="Ask MAGiE AI anything...">

        <button onclick="sendMessage()">
            ➤
        </button>

    </div>

</div>

<style>

/* MAIN CHAT WINDOW */
.magi-chat-container{
    width:100%;
    height:100%;

    background:#ffffff;

    border-radius:0;

    overflow:hidden;

    box-shadow:
        0 10px 40px rgba(0,0,0,0.15);

    display:flex;
    flex-direction:column;

    font-family:'Segoe UI',Tahoma,sans-serif;

    border:1px solid #e9ecef;
}


/* LOGO */
.magi-logo-container{
    margin-bottom:15px;
}

.magi-logo{
    width:72px;
    height:72px;

    border-radius:50%;

    object-fit:cover;

    background:white;

    padding:4px;

    box-shadow:0 4px 15px rgba(0,0,0,0.25);
}

/* AI INTRO MESSAGE */
.magi-ai-message{
    font-size:14px;

    line-height:1.7;

    color:#f8f9fa;

    font-weight:400;
}

/* CHAT BODY */
#magi-chat-body{
    flex:1;

    overflow-y:auto;

    padding:18px;

    background:#f7f9fc;

    display:flex;
    flex-direction:column;
}

/* USER MESSAGE */
.magi-user-msg{
    background:linear-gradient(135deg,#0d6efd,#0056d6);
    color:white;
    padding:12px 16px;
    border-radius:18px 18px 4px 18px;
    margin:8px 0;
    width:fit-content;
    max-width:80%;
    margin-left:auto;
    font-size:14px;
    line-height:1.5;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}

/* BOT MESSAGE */
.magi-bot-msg{
    background:white;
    color:#2c3e50;
    padding:12px 16px;
    border-radius:18px 18px 18px 4px;
    margin:8px 0;
    width:fit-content;
    max-width:80%;
    font-size:14px;
    line-height:1.6;
    border:1px solid #e9ecef;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
}

/* BOT MESSAGE FORMATTING */

.magi-bot-msg h1,
.magi-bot-msg h2,
.magi-bot-msg h3,
.magi-bot-msg h4{
    margin-top:14px;
    margin-bottom:10px;
    color:#0d1b2a;
}

.magi-bot-msg p{
    margin:8px 0;
}

.magi-bot-msg ul,
.magi-bot-msg ol{
    padding-left:22px;
    margin:10px 0;
}

.magi-bot-msg li{
    margin-bottom:6px;
}

.magi-bot-msg pre{
    background:#111827;
    color:#f8fafc;
    padding:14px;
    border-radius:12px;
    overflow-x:auto;
    margin-top:12px;
}

.magi-bot-msg code{
    font-family:Consolas, monospace;
    font-size:13px;
}

.magi-bot-msg table{
    width:100%;
    border-collapse:collapse;
    margin-top:12px;
    font-size:13px;
}

.magi-bot-msg table th,
.magi-bot-msg table td{
    border:1px solid #dce3ea;
    padding:8px;
    text-align:left;
}

.magi-bot-msg a{
    color:#0d6efd;
    text-decoration:none;
}

.magi-bot-msg a:hover{
    text-decoration:underline;
}

.magi-bot-msg blockquote{
    border-left:4px solid #0d6efd;
    padding-left:12px;
    margin:12px 0;
    color:#555;
    font-style:italic;
}

/* QUICK SUGGESTIONS */
.magi-suggestions{
    padding:12px 15px;

    background:white;

    border-top:1px solid #f1f1f1;

    display:flex;

    flex-wrap:wrap;

    gap:8px;
}

.magi-suggestions button{
    border:none;

    background:#eef4ff;

    color:#0d6efd;

    padding:8px 14px;

    border-radius:20px;

    cursor:pointer;

    font-size:13px;

    font-weight:500;

    transition:all 0.25s ease;
}

.magi-suggestions button:hover{
    background:#0d6efd;

    color:white;

    transform:translateY(-1px);
}

/* FOOTER */
.magi-footer{
    display:flex;

    align-items:center;

    gap:10px;

    padding:14px;

    background:white;

    border-top:1px solid #ececec;
}

/* FILE BUTTON */
#magi-file-upload{
    width:42px;
}

/* INPUT */
#magi-user-input{
    flex:1;

    border:1px solid #dce3ea;

    border-radius:30px;

    padding:12px 16px;

    outline:none;

    font-size:14px;

    transition:all 0.2s ease;

    background:#fafafa;
}

#magi-user-input:focus{
    border-color:#0d6efd;

    background:white;

    box-shadow:0 0 0 3px rgba(13,110,253,0.1);
}

/* SEND BUTTON */
.magi-footer button{
    width:46px;
    height:46px;

    border:none;

    border-radius:50%;

    background:linear-gradient(135deg,#0d6efd,#0047b3);

    color:white;

    cursor:pointer;

    font-size:18px;

    display:flex;
    align-items:center;
    justify-content:center;

    transition:all 0.25s ease;

    box-shadow:0 4px 12px rgba(13,110,253,0.3);
}

.magi-footer button:hover{
    transform:scale(1.05);

    box-shadow:0 6px 16px rgba(13,110,253,0.4);
}

/* SCROLLBAR */
#magi-chat-body::-webkit-scrollbar{
    width:6px;
}

#magi-chat-body::-webkit-scrollbar-thumb{
    background:#cfd8e3;

    border-radius:10px;
}

.magi-error-msg{
    background:#ffebee;

    color:#d32f2f;

    padding:12px 16px;

    border-radius:14px;

    margin-top:10px;

    font-size:14px;

    border:1px solid #ffcdd2;
}


.magi-upload-btn{
    width:42px;
    height:42px;

    border-radius:50%;

    background:#f1f4f9;

    display:flex;
    align-items:center;
    justify-content:center;

    cursor:pointer;

    font-size:18px;

    transition:all 0.25s ease;

    border:1px solid #dce3ea;
}

.magi-upload-btn:hover{
    background:#0d6efd;

    color:white;

    transform:scale(1.05);
}
</style>

<!-- MARKDOWN PARSER -->
<!--<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>-->
<script src="/studyconnect/js/marked.min.js"></script>

<!-- HTML SANITIZER -->
<!--<script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.8/dist/purify.min.js"></script>-->
<script src="/studyconnect/js/purify.min.js"></script>
<!-- CODE HIGHLIGHT -->
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">-->
<link rel="stylesheet" href="/studyconnect/css/github-dark.min.css">

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>-->
<script src="/studyconnect/js/highlight.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.2.6/purify.min.js"></script>

<script>

function quickAsk(question){

    document.getElementById("magi-user-input").value = question;

    sendMessage();
}

marked.setOptions({
    breaks: true
});

async function sendMessage(){

    let inputBox = document.getElementById("magi-user-input");

    let chatBody = document.getElementById("magi-chat-body");

    let message = inputBox.value.trim();

    if(message === ""){
        return;
    }

	// USER MESSAGE
/*
	chatBody.innerHTML += `
		<div class="magi-user-msg">
			${message}
		</div>
	`;
*/
	const userDiv = document.createElement("div");
	userDiv.className = "magi-user-msg";
	userDiv.textContent = message;
	chatBody.appendChild(userDiv);

    inputBox.value = "";

    chatBody.scrollTop = chatBody.scrollHeight;

    try{

        const response = await fetch('chatbot/api/send_message.php', {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json'
            },

            body: JSON.stringify({
                message: message
            })

        });

        const data = await response.json();

		// BOT RESPONSE
		/* 
		chatBody.innerHTML += `
			<div class="magi-bot-msg">
				${data.reply}
			</div>
		`;
		*/
		
		// Convert markdown -> safe HTML
		const cleanHTML = DOMPurify.sanitize(
			marked.parse(data.reply)
		);

		// Create bot message div
		const botDiv = document.createElement("div");

		botDiv.className = "magi-bot-msg";

		botDiv.innerHTML = cleanHTML;

		// Add to chat
		chatBody.appendChild(botDiv);

		// Highlight code blocks
		botDiv.querySelectorAll('pre code').forEach((el) => {
			hljs.highlightElement(el);
		});

        chatBody.scrollTop = chatBody.scrollHeight;

    }
    catch(error){

        console.log(error);

		chatBody.innerHTML += `
			<div class="magi-error-msg">
				Error connecting to AI server.
			</div>
		`;
    }
}

</script>
