<!-- sidebar.php -->

<?php
require_once 'includes/permission_helper.php';
?>

<div class="sidebar">

    <ul>

        <?php if (userCan('dashboard.view')) : ?>

            <li>
                <a href="dashboard.php" target="contentFrame">
                    📊 Dashboard
                </a>
            </li>

        <?php endif; ?>


        <?php if (userCan('student.create')) : ?>

            <li>
                <a href="student_form.php" target="contentFrame">
                    📝 New Student
                </a>
            </li>

        <?php endif; ?>


        <?php if (userCan('student.view')) : ?>

            <li>
                <a href="student_list.php" target="contentFrame">
                    🎓 Student List
                </a>
            </li>

        <?php endif; ?>


        <?php if (userCan('shortlisting.view')) : ?>

            <li>
                <a href="shortlisting.php" target="contentFrame">
                    🎯 Shortlisting
                </a>
            </li>

        <?php endif; ?>


        <?php if (userCan('student.view')) : ?>

            <li>
                <a href="messages.php" target="contentFrame">
                    🏛️ Universities
                </a>
            </li>

            <li>
                <a href="messages.php" target="contentFrame">
                    💬 Messages
                </a>
            </li>

            <li>
                <a href="messages.php" target="contentFrame">
                    📚 Resources
                </a>
            </li>

        <?php endif; ?>


        <?php if (userCan('reports.view')) : ?>

            <li>
                <a href="reports.php" target="contentFrame">
                    📈 Reports
                </a>
            </li>

        <?php endif; ?>


        <?php if (isAdministrator()) : ?>

            <li class="menu-heading">
                Administration
            </li>

            <li>
                <a href="users_list.php" target="contentFrame">
                    👥 User Management
                </a>
            </li>

            <li>
                <a href="role_list.php" target="contentFrame">
                    🔐 Role Management
                </a>
            </li>

        <?php endif; ?>

    </ul>

</div>


<!-- Floating MAGI AI Button -->
<div id="magiChatButton" onclick="openMagiChat()">

    <img src="chatbot/assets/icons/magi-logo.png"
         class="magi-button-logo">

    <span>MAGiE AI</span>

</div>

<style>
/* FLOATING MAGI BUTTON */
#magiChatButton{
    position:fixed;

    bottom:20px;
    right:20px;

    display:flex;
    align-items:center;
    gap:10px;

    background:linear-gradient(135deg,#0d6efd,#0047b3);

    color:white;

    padding:12px 18px;

    border-radius:50px;

    cursor:pointer;

    font-family:'Segoe UI',sans-serif;

    font-size:14px;
    font-weight:600;

    box-shadow:0 8px 25px rgba(0,0,0,0.25);

    z-index:9999;

    transition:all 0.25s ease;
}

#magiChatButton:hover{
    transform:translateY(-2px) scale(1.03);

    box-shadow:0 12px 28px rgba(0,0,0,0.30);
}

/* LOGO */
.magi-button-logo{
    width:38px;
    height:38px;

    border-radius:50%;

    object-fit:cover;

    background:white;

    padding:3px;
}

/* CHAT WINDOW */
#magiChatWindow{
    position:fixed;

    bottom:90px;
    right:20px;

    width:420px;
    height:700px;

    background:white;

    border-radius:22px;

    overflow:hidden;

    display:none;

    flex-direction:column;

    box-shadow:0 15px 50px rgba(0,0,0,0.25);

    z-index:10000;

    border:1px solid #e9ecef;

    animation:magiFadeIn 0.25s ease;
}

/* HEADER */
.magi-window-header{
    height:65px;

    background:linear-gradient(135deg,#0d6efd,#0047b3);

    color:white;

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:0 15px;
}

.magi-header-left{
    display:flex;
    align-items:center;
    gap:12px;
}

.magi-header-logo{
    width:42px;
    height:42px;

    border-radius:50%;

    background:white;

    padding:2px;
}

.magi-header-title{
    font-size:15px;
    font-weight:600;
}

.magi-header-subtitle{
    font-size:12px;
    opacity:0.9;
}

/* ACTION BUTTONS */
.magi-window-actions{
    display:flex;
    gap:8px;
}

.magi-window-actions button{
    width:32px;
    height:32px;

    border:none;

    border-radius:8px;

    background:rgba(255,255,255,0.15);

    color:white;

    cursor:pointer;

    transition:0.2s;
}

.magi-window-actions button:hover{
    background:rgba(255,255,255,0.3);
}

/* CHAT CONTENT */

.magi-chat-content{
    flex:1;

    display:flex;

    overflow:hidden;

    background:#f7f9fc;
}


/* MAXIMIZED */
.magi-maximized{
    width:95vw !important;
    height:95vh !important;

    right:2.5vw !important;
    bottom:2.5vh !important;
}

/* ANIMATION */
@keyframes magiFadeIn{
    from{
        opacity:0;
        transform:translateY(20px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}
</style>

<script>

function openMagiChat(){

    document.getElementById("magiChatWindow").style.display = "flex";
}

function closeMagiChat(){

    document.getElementById("magiChatWindow").style.display = "none";
}

function minimizeMagiChat(){

    document.getElementById("magiChatWindow").style.display = "none";
}

function toggleMaximizeMagiChat(){

    document
        .getElementById("magiChatWindow")
        .classList
        .toggle("magi-maximized");
}
/*
function openMagiChat(){

    window.open(
        'chatbot/chatbox.php',
        'MAGI_AI',
        'width=500,height=750'
    );

}
*/
</script>


<div id="magiChatWindow">

    <!-- HEADER -->
    <div class="magi-window-header">

        <div class="magi-header-left">

            <img src="chatbot/assets/icons/magi-logo.png"
                 class="magi-header-logo">

            <div>
                <div class="magi-header-title">
                    MAGiE AI
                </div>

                <div class="magi-header-subtitle">
                    Study Abroad Assistant
                </div>
            </div>

        </div>

        <div class="magi-window-actions">

            <button onclick="minimizeMagiChat()">─</button>

            <button onclick="toggleMaximizeMagiChat()">▢</button>

            <button onclick="closeMagiChat()">✕</button>

        </div>

    </div>

    <!-- CHAT CONTENT -->
    <div class="magi-chat-content">

        <!-- YOUR EXISTING CHATBOX -->
        <?php include 'chatbot/chatbox.php'; ?>

    </div>

</div>

