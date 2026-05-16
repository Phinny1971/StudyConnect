function renderMarkdown(text)
{
    text = text.replace(
        /\*\*(.*?)\*\*/g,
        '<strong>$1</strong>'
    );

    text = text.replace(
        /\*(.*?)\*/g,
        '<em>$1</em>'
    );

    text = text.replace(
        /\n/g,
        '<br>'
    );

    return text;
}