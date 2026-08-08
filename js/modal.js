function showModal(options) {

    let settings = {
        title: "Message",
        message: "",
        showYesNo: false,
        showOk: true,
        onYes: null,
        onNo: null,
        onOk: null
    };

    settings = { ...settings, ...options };

    const modal = document.getElementById("customModal");

    const title = document.getElementById("modalTitle");
    const message = document.getElementById("modalMessage");

    const yesBtn = document.getElementById("modalYesBtn");
    const noBtn = document.getElementById("modalNoBtn");
    const okBtn = document.getElementById("modalOkBtn");

    title.textContent = settings.title;
    message.textContent = settings.message;

    yesBtn.style.display = settings.showYesNo ? "inline-block" : "none";
    noBtn.style.display = settings.showYesNo ? "inline-block" : "none";
    okBtn.style.display = settings.showOk ? "inline-block" : "none";

    yesBtn.onclick = null;
    noBtn.onclick = null;
    okBtn.onclick = null;

    yesBtn.onclick = function () {
        closeMsgModal();
        if (settings.onYes) settings.onYes();
    };

    noBtn.onclick = function () {
        closeMsgModal();
        if (settings.onNo) settings.onNo();
    };

    okBtn.onclick = function () {
        closeMsgModal();
        if (settings.onOk) settings.onOk();
    };

    modal.style.display = "flex";
}

function closeMsgModal() {
    document.getElementById("customModal").style.display = "none";
}