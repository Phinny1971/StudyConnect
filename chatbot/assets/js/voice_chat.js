const recognition =
    new(window.SpeechRecognition ||
        window.webkitSpeechRecognition)();

recognition.lang = 'en-US';

recognition.continuous = false;

recognition.interimResults = false;

function startVoiceInput()
{
    recognition.start();
}

recognition.onresult = function(event)
{
    const transcript =
        event.results[0][0].transcript;

    document.getElementById(
        'magi-user-input'
    ).value = transcript;
};

recognition.onerror = function(event)
{
    console.log(event.error);
};